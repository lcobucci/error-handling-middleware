<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Provides a custom URI for the documentation of the problem
 *
 * @see https://tools.ietf.org/html/rfc7807#section-3.1
 */
interface Typed extends Throwable
{
    public function getTypeUri(): string;
}
