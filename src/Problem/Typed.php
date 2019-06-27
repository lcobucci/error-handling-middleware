<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

interface Typed extends Throwable
{
    public function getTypeUri(): string;
}
