<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\DebugInfoStrategy;

use Lcobucci\ErrorHandling\DebugInfoStrategy;
use Throwable;

final class NoDebugInfo implements DebugInfoStrategy
{
    /**
     * {@inheritDoc}
     */
    public function extractDebugInfo(Throwable $error): ?array
    {
        return null;
    }
}
