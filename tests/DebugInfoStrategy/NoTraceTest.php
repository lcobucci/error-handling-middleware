<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\DebugInfoStrategy;

use InvalidArgumentException;
use Lcobucci\ErrorHandling\DebugInfoStrategy\NoTrace;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @coversDefaultClass \Lcobucci\ErrorHandling\DebugInfoStrategy\NoTrace */
final class NoTraceTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::extractDebugInfo
     * @covers ::format
     * @covers ::streamStack
     */
    public function extractDebugInfoShouldConvertExceptionInfoWithoutTheTrace(): void
    {
        $strategy = new NoTrace();

        self::assertSame(
            [
                'class'   => RuntimeException::class,
                'code'    => 11,
                'message' => 'Testing',
                'file'    => __FILE__,
                'line'    => __LINE__ + 2,
            ],
            $strategy->extractDebugInfo(new RuntimeException('Testing', 11)),
        );
    }

    /**
     * @test
     *
     * @covers ::extractDebugInfo
     * @covers ::format
     * @covers ::streamStack
     */
    public function extractDebugInfoShouldAlsoReturnPreviousExceptions(): void
    {
        $strategy = new NoTrace();

        self::assertSame(
            [
                'class'   => RuntimeException::class,
                'code'    => 11,
                'message' => 'Testing',
                'file'    => __FILE__,
                'line'    => __LINE__ + 19,
                'stack'   => [
                    [
                        'class'   => InvalidArgumentException::class,
                        'code'    => 25,
                        'message' => 'Oh no!',
                        'file'    => __FILE__,
                        'line'    => __LINE__ + 15,
                    ],
                    [
                        'class'   => LogicException::class,
                        'code'    => 0,
                        'message' => 'Bummer',
                        'file'    => __FILE__,
                        'line'    => __LINE__ + 11,
                    ],
                ],
            ],
            $strategy->extractDebugInfo(
                new RuntimeException(
                    'Testing',
                    11,
                    new InvalidArgumentException(
                        'Oh no!',
                        25,
                        new LogicException('Bummer'),
                    ),
                ),
            ),
        );
    }
}
