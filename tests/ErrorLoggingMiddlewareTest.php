<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests;

use Lcobucci\ErrorHandling\ErrorLoggingMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

/**
 * @coversDefaultClass \Lcobucci\ErrorHandling\ErrorLoggingMiddleware
 */
final class ErrorLoggingMiddlewareTest extends TestCase
{
    /**
     * @var LoggerInterface&MockObject
     */
    private $logger;

    /**
     * @before
     */
    public function createLogger(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     */
    public function processShouldLogAllExceptionsOrErrorsThatHappenedDuringRequestHandling(): void
    {
        $error = new RuntimeException('Testing');

        $this->logger->expects(self::once())
                     ->method('debug')
                     ->with('Error happened while processing request', ['exception' => $error]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException($error);

        $middleware = new ErrorLoggingMiddleware($this->logger);

        $this->expectExceptionObject($error);
        $middleware->process(new ServerRequest(), $handler);
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::process
     */
    public function processShouldReturnResponseWhenEverythingIsAlright(): void
    {
        $this->logger->expects(self::never())->method('debug');

        $response = new Response();

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        $middleware = new ErrorLoggingMiddleware($this->logger);

        self::assertSame($response, $middleware->process(new ServerRequest(), $handler));
    }
}
