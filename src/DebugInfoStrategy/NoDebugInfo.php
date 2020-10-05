<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\DebugInfoStrategy;

use Lcobucci\ErrorHandling\DebugInfoStrategy;
use Throwable;

final class NoDebugInfo implements DebugInfoStrategy
{
    /** @inheritDoc */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function extractDebugInfo(Throwable $error): ?array
    {
        return null;
    }
}
