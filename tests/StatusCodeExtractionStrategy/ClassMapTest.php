<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\StatusCodeExtractionStrategy;

use Fig\Http\Message\StatusCodeInterface;
use Lcobucci\ErrorHandling\Problem;
use Lcobucci\ErrorHandling\StatusCodeExtractionStrategy\ClassMap;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * @coversDefaultClass \Lcobucci\ErrorHandling\StatusCodeExtractionStrategy\ClassMap
 */
final class ClassMapTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::extractStatusCode
     */
    public function extractStatusCodeShouldUseGivenMapToRetrieveTheCode(): void
    {
        $extractor = new ClassMap([RuntimeException::class => StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE]);

        self::assertSame(
            StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE,
            $extractor->extractStatusCode(new RuntimeException())
        );
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::extractStatusCode
     */
    public function extractStatusCodeShouldUseExceptionCodeWhenItIsNotSetInTheMap(): void
    {
        $extractor = new ClassMap([]);

        self::assertSame(
            StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE,
            $extractor->extractStatusCode(new RuntimeException('', StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE))
        );
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::extractStatusCode
     */
    public function extractStatusCodeShouldFallbackToInternalServerError(): void
    {
        $extractor = new ClassMap([]);

        self::assertSame(
            StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
            $extractor->extractStatusCode(new RuntimeException())
        );
    }

    /**
     * @test
     * @dataProvider defaultConversions
     *
     * @covers ::__construct
     * @covers ::extractStatusCode
     */
    public function extractStatusCodeShouldUseDefaultClassMapWhenNothingIsProvided(
        Throwable $error,
        int $expected
    ): void {
        $extractor = new ClassMap();

        self::assertSame($expected, $extractor->extractStatusCode($error));
    }

    /**
     * @return array<string, array<Throwable|int>>
     */
    public function defaultConversions(): iterable
    {
        yield Problem\InvalidRequest::class => [
            $this->createMock(Problem\InvalidRequest::class),
            StatusCodeInterface::STATUS_BAD_REQUEST,
        ];

        yield Problem\AuthorizationRequired::class => [
            $this->createMock(Problem\AuthorizationRequired::class),
            StatusCodeInterface::STATUS_UNAUTHORIZED,
        ];

        yield Problem\Forbidden::class => [
            $this->createMock(Problem\Forbidden::class),
            StatusCodeInterface::STATUS_FORBIDDEN,
        ];

        yield Problem\ResourceNotFound::class => [
            $this->createMock(Problem\ResourceNotFound::class),
            StatusCodeInterface::STATUS_NOT_FOUND,
        ];

        yield Problem\Conflict::class => [
            $this->createMock(Problem\Conflict::class),
            StatusCodeInterface::STATUS_CONFLICT,
        ];

        yield Problem\ResourceNoLongerAvailable::class => [
            $this->createMock(Problem\ResourceNoLongerAvailable::class),
            StatusCodeInterface::STATUS_GONE,
        ];

        yield Problem\UnprocessableRequest::class => [
            $this->createMock(Problem\UnprocessableRequest::class),
            StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
        ];

        yield Problem\ServiceUnavailable::class => [
            $this->createMock(Problem\ServiceUnavailable::class),
            StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE,
        ];
    }
}
