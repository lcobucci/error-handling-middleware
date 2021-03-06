<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to missing auth information (credentials or valid access token)
 */
interface AuthorizationRequired extends Throwable
{
}
