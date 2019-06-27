<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\SampleProblem;

use Lcobucci\ErrorHandling\Problem\Detailed as DetailedInterface;
use Lcobucci\ErrorHandling\Problem\Titled as TitledInterface;
use Lcobucci\ErrorHandling\Problem\Typed as TypedInterface;
use RuntimeException;

final class All extends RuntimeException implements TypedInterface, TitledInterface, DetailedInterface
{
    public function getTypeUri(): string
    {
        return 'https://example.com/probs/out-of-credit';
    }

    public function getTitle(): string
    {
        return 'You do not have enough credit.';
    }

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
