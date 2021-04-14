<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\DebugInfoStrategy;

use Generator;
use Lcobucci\ErrorHandling\DebugInfoStrategy;
use Throwable;

use function iterator_to_array;

final class NoTrace implements DebugInfoStrategy
{
    /** @inheritDoc */
    public function extractDebugInfo(Throwable $error): ?array
    {
        $debugInfo = $this->format($error);
        $stack     = iterator_to_array($this->streamStack($error->getPrevious()), false);

        if ($stack !== []) {
            $debugInfo['stack'] = $stack;
        }

        return $debugInfo;
    }

    /** @return Generator<array<string, string|int>> */
    private function streamStack(?Throwable $previous): Generator
    {
        if ($previous === null) {
            return;
        }

        yield $this->format($previous);
        yield from $this->streamStack($previous->getPrevious());
    }

    /** @return array<string, string|int> */
    private function format(Throwable $error): array
    {
        return [
            'class'   => $error::class,
            'code'    => $error->getCode(),
            'message' => $error->getMessage(),
            'file'    => $error->getFile(),
            'line'    => $error->getLine(),
        ];
    }
}
