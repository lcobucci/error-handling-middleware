<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling;

use Throwable;

/**
 * Defines how the debug information should be extracted from an error/exception
 */
interface DebugInfoStrategy
{
    /**
     * @return array<string, mixed>|null
     */
    public function extractDebugInfo(Throwable $error): ?array;
}
