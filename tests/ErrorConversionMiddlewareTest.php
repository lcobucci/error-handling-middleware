<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Lcobucci\ErrorHandling\DebugInfoStrategy;
use Lcobucci\ErrorHandling\DebugInfoStrategy\NoDebugInfo;
use Lcobucci\ErrorHandling\DebugInfoStrategy\NoTrace;
use Lcobucci\ErrorHandling\ErrorConversionMiddleware;
use Lcobucci\ErrorHandling\StatusCodeExtractionStrategy\ClassMap;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

/**
 * @coversDefaultClass \Lcobucci\ErrorHandling\ErrorConversionMiddleware
 *
 * @uses \Lcobucci\ErrorHandling\DebugInfoStrategy\NoDebugInfo
 * @uses \Lcobucci\ErrorHandling\DebugInfoStrategy\NoTrace
 * @uses \Lcobucci\ErrorHandling\StatusCodeExtractionStrategy\ClassMap
 */
final class ErrorConversionMiddlewareTest extends TestCase
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClassMap
     */
    private $statusCodeExtractor;

    /**
     * @before
     */
    public function createDependencies(): void
    {
        $this->responseFactory     = new ResponseFactory();
        $this->statusCodeExtractor = new ClassMap();
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     */
    public function processShouldJustReturnTheResponseWhenEverythingIsAlright(): void
    {
        $response = new Response();

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $middleware = new ErrorConversionMiddleware(
            $this->responseFactory,
            new NoDebugInfo(),
            $this->statusCodeExtractor
        );

        self::assertSame($response, $middleware->process(new ServerRequest(), $handler));
    }

    /**
     * @test
     * @dataProvider possibleConversions
     *
     * @covers ::__construct
     * @covers ::process
     * @covers ::generateResponse
     * @covers ::extractData
     *
     * @param array<string, mixed> $expectedData
     */
    public function processShouldConvertTheExceptionIntoAnUnformattedResponseWithTheProblemDetails(
        Throwable $error,
        int $expectedStatusCode,
        array $expectedData
    ): void {
        $response = $this->handleProcessWithError(new ServerRequest(), $error);

        self::assertInstanceOf(UnformattedResponse::class, $response);
        self::assertSame($expectedStatusCode, $response->getStatusCode());
        self::assertSame($expectedData, $response->getUnformattedContent());
    }

    /**
     * @return array<string, array<Throwable|array<string, mixed>>>
     */
    public function possibleConversions(): iterable
    {
        yield 'no customisation' => [
            new RuntimeException('Item #1 was not found', StatusCodeInterface::STATUS_NOT_FOUND),
            StatusCodeInterface::STATUS_NOT_FOUND,
            [
                'type' => 'https://httpstatuses.com/404',
                'title' => 'Not Found',
                'details' => 'Item #1 was not found',
            ],
        ];

        yield 'typed exceptions' => [
            new SampleProblem\Typed(
                'Your current balance is 30, but that costs 50.',
                StatusCodeInterface::STATUS_FORBIDDEN
            ),
            StatusCodeInterface::STATUS_FORBIDDEN,
            [
                'type' => 'https://example.com/probs/out-of-credit',
                'title' => 'Forbidden',
                'details' => 'Your current balance is 30, but that costs 50.',
            ],
        ];

        yield 'titled exceptions' => [
            new SampleProblem\Titled(
                'Your current balance is 30, but that costs 50.',
                StatusCodeInterface::STATUS_FORBIDDEN
            ),
            StatusCodeInterface::STATUS_FORBIDDEN,
            [
                'type' => 'https://httpstatuses.com/403',
                'title' => 'You do not have enough credit.',
                'details' => 'Your current balance is 30, but that costs 50.',
            ],
        ];

        yield 'detailed exceptions' => [
            new SampleProblem\Detailed(
                'Your current balance is 30, but that costs 50.',
                StatusCodeInterface::STATUS_FORBIDDEN
            ),
            StatusCodeInterface::STATUS_FORBIDDEN,
            [
                'type' => 'https://httpstatuses.com/403',
                'title' => 'Forbidden',
                'details' => 'Your current balance is 30, but that costs 50.',
                'balance' => 30,
                'cost' => 50,
            ],
        ];

        yield 'typed+titled+detailed exceptions' => [
            new SampleProblem\All(
                'Your current balance is 30, but that costs 50.',
                StatusCodeInterface::STATUS_FORBIDDEN
            ),
            StatusCodeInterface::STATUS_FORBIDDEN,
            [
                'type' => 'https://example.com/probs/out-of-credit',
                'title' => 'You do not have enough credit.',
                'details' => 'Your current balance is 30, but that costs 50.',
                'balance' => 30,
                'cost' => 50,
            ],
        ];
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     * @covers ::generateResponse
     * @covers ::extractData
     */
    public function processShouldKeepOriginalErrorAsResponseAttribute(): void
    {
        $error    = new RuntimeException();
        $response = $this->handleProcessWithError(new ServerRequest(), $error);

        self::assertInstanceOf(UnformattedResponse::class, $response);

        $attributes = $response->getAttributes();
        self::assertArrayHasKey('error', $attributes);
        self::assertSame($error, $attributes['error']);
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     * @covers ::generateResponse
     * @covers ::extractData
     */
    public function processShouldAddDebugInfoData(): void
    {
        $response = $this->handleProcessWithError(new ServerRequest(), new RuntimeException(), new NoTrace());

        self::assertInstanceOf(UnformattedResponse::class, $response);
        self::assertArrayHasKey('_debug', $response->getUnformattedContent());
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     * @covers ::generateResponse
     * @covers ::extractData
     */
    public function processShouldModifyTheContentTypeHeaderForJson(): void
    {
        $request = (new ServerRequest())->withAddedHeader('Accept', 'application/json')
                                        ->withAddedHeader('Accept-Charset', 'UTF-8');

        $response = $this->handleProcessWithError($request, new RuntimeException());

        self::assertSame('application/problem+json; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     * @covers ::generateResponse
     * @covers ::extractData
     */
    public function processShouldModifyTheContentTypeHeaderForXml(): void
    {
        $request = (new ServerRequest())->withAddedHeader('Accept', 'application/xml')
                                        ->withAddedHeader('Accept-Charset', 'UTF-8');

        $response = $this->handleProcessWithError($request, new RuntimeException());

        self::assertSame('application/problem+xml; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    private function handleProcessWithError(
        ServerRequestInterface $request,
        Throwable $error,
        ?DebugInfoStrategy $debugInfoStrategy = null
    ): ResponseInterface {
        $middleware = new ErrorConversionMiddleware(
            $this->responseFactory,
            $debugInfoStrategy ?? new NoDebugInfo(),
            $this->statusCodeExtractor
        );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException($error);

        return $middleware->process($request, $handler);
    }
}
