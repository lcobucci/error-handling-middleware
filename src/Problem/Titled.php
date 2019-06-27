<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Problem;

use Throwable;

interface Titled extends Throwable
{
    public function getTitle(): string;
}
