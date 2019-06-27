<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to malformed requests (syntax issues)
 */
interface InvalidRequest extends Throwable
{
}
