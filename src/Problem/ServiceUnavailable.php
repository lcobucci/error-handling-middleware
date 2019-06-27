<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to availability issues (maintenance or dependency issues)
 */
interface ServiceUnavailable extends Throwable
{
}
