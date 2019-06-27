<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\Tests\SampleProblem;

use Lcobucci\ErrorHandling\Problem\Typed as TypedInterface;
use RuntimeException;

final class Typed extends RuntimeException implements TypedInterface
{
    public function getTypeUri(): string
    {
        return 'https://example.com/probs/out-of-credit';
    }
}
