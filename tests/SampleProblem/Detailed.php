<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\SampleProblem;

use Lcobucci\ErrorHandling\Problem\Detailed as DetailedInterface;
use RuntimeException;

final class Detailed extends RuntimeException implements DetailedInterface
{
    /**
     * {@inheritDoc}
     */
    public function getExtraDetails(): array
    {
        return [
            'balance' => 30,
            'cost' => 50,
        ];
    }
}
