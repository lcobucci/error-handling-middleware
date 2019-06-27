<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling;

use Throwable;

/**
 * Defines how the translation of errors/exceptions into HTTP status codes should happen
 */
interface StatusCodeExtractionStrategy
{
    public function extractStatusCode(Throwable $error): int;
}
