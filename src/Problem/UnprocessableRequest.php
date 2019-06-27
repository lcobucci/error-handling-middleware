<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Marker interface to be used in exceptions related to syntactically correct but semantically invalid requests
 */
interface UnprocessableRequest extends Throwable
{
}
