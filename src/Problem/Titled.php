<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Provides a custom (human-readable) summary of the problem
 *
 * The provided title SHOULD NOT vary from occurrence to occurrence.
 *
 * @see https://tools.ietf.org/html/rfc7807#section-3.1
 */
interface Titled extends Throwable
{
    public function getTitle(): string;
}
