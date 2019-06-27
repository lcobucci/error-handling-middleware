<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to resource conflicts (version mismatch or duplicated data)
 */
interface Conflict extends Throwable
{
}
