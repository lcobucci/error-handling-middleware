<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\SampleProblem;

use Lcobucci\ErrorHandling\Problem\Titled as TitledInterface;
use RuntimeException;

final class Titled extends RuntimeException implements TitledInterface
{
    public function getTitle(): string
    {
        return 'You do not have enough credit.';
    }
}
