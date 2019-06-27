<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to forbidden operations (lack of permission)
 */
interface Forbidden extends Throwable
{
}
