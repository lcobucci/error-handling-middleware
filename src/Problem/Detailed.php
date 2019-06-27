<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

interface Detailed extends Throwable
{
    /**
     * @return array<string, mixed>
     */
    public function getExtraDetails(): array;
}
