<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling;

use Throwable;

interface StatusCodeExtractionStrategy
{
    public function extractStatusCode(Throwable $error): int;
}
