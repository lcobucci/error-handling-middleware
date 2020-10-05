<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling;

use Lcobucci\ContentNegotiation\UnformattedResponse;
use Lcobucci\ErrorHandling\Problem\Detailed;
use Lcobucci\ErrorHandling\Problem\Titled;
use Lcobucci\ErrorHandling\Problem\Typed;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function array_key_exists;

final class ErrorConversionMiddleware implements MiddlewareInterface
{
    private const CONTENT_TYPE_CONVERSION = [
        'application/json' => 'application/problem+json',
        'application/xml' => 'application/problem+xml',
    ];

    private const STATUS_URL = 'https://httpstatuses.com/';

    private ResponseFactoryInterface $responseFactory;
    private DebugInfoStrategy $debugInfoStrategy;
    private StatusCodeExtractionStrategy $statusCodeExtractor;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        DebugInfoStrategy $debugInfoStrategy,
        StatusCodeExtractionStrategy $statusCodeExtractor
    ) {
        $this->responseFactory     = $responseFactory;
        $this->debugInfoStrategy   = $debugInfoStrategy;
        $this->statusCodeExtractor = $statusCodeExtractor;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $error) {
            $response = $this->generateResponse($request, $error);

            return new UnformattedResponse(
                $response,
                $this->extractData($error, $response),
                ['error' => $error]
            );
        }
    }

    private function generateResponse(ServerRequestInterface $request, Throwable $error): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($this->statusCodeExtractor->extractStatusCode($error));

        $accept = $request->getHeaderLine('Accept');

        if (! array_key_exists($accept, self::CONTENT_TYPE_CONVERSION)) {
            return $response;
        }

        return $response->withAddedHeader(
            'Content-Type',
            self::CONTENT_TYPE_CONVERSION[$accept] . '; charset=' . $request->getHeaderLine('Accept-Charset')
        );
    }

    /** @return array<string, mixed> */
    private function extractData(Throwable $error, ResponseInterface $response): array
    {
        $data = [
            'type' => $error instanceof Typed ? $error->getTypeUri() : self::STATUS_URL . $response->getStatusCode(),
            'title' => $error instanceof Titled ? $error->getTitle() : $response->getReasonPhrase(),
            'details' => $error->getMessage(),
        ];

        if ($error instanceof Detailed) {
            $data += $error->getExtraDetails();
        }

        $debugInfo = $this->debugInfoStrategy->extractDebugInfo($error);

        if ($debugInfo !== null) {
            $data['_debug'] = $debugInfo;
        }

        return $data;
    }
}
