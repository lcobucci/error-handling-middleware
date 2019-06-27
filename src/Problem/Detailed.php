<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

/**
 * Provides extension members to the problem details
 *
 * @see https://tools.ietf.org/html/rfc7807#section-3.2
 */
interface Detailed extends Throwable
{
    /**
     * @return array<string, mixed>
     */
    public function getExtraDetails(): array;
}
