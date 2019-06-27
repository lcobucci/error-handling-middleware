<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling;

use Throwable;

interface DebugInfoStrategy
{
    /**
     * @return array<string, mixed>|null
     */
    public function extractDebugInfo(Throwable $error): ?array;
}
