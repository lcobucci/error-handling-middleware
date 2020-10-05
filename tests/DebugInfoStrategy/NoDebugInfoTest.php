<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\DebugInfoStrategy;

use Lcobucci\ErrorHandling\DebugInfoStrategy\NoDebugInfo;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @coversDefaultClass \Lcobucci\ErrorHandling\DebugInfoStrategy\NoDebugInfo */
final class NoDebugInfoTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::extractDebugInfo
     */
    public function extractDebugInfoShouldAlwaysReturnNull(): void
    {
        $strategy = new NoDebugInfo();

        self::assertNull($strategy->extractDebugInfo(new RuntimeException()));
    }
}
