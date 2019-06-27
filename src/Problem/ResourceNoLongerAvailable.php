<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to removed resource (removed/archived items)
 */
interface ResourceNoLongerAvailable extends Throwable
{
}
