<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\StatusCodeExtractionStrategy;

use Fig\Http\Message\StatusCodeInterface;
use Lcobucci\ErrorHandling\Problem;
use Lcobucci\ErrorHandling\StatusCodeExtractionStrategy;
use Throwable;

use function assert;
use function is_int;

final class ClassMap implements StatusCodeExtractionStrategy
{
    private const DEFAULT_MAP = [
        Problem\InvalidRequest::class => StatusCodeInterface::STATUS_BAD_REQUEST,
        Problem\AuthorizationRequired::class => StatusCodeInterface::STATUS_UNAUTHORIZED,
        Problem\Forbidden::class => StatusCodeInterface::STATUS_FORBIDDEN,
        Problem\ResourceNotFound::class => StatusCodeInterface::STATUS_NOT_FOUND,
        Problem\Conflict::class => StatusCodeInterface::STATUS_CONFLICT,
        Problem\ResourceNoLongerAvailable::class => StatusCodeInterface::STATUS_GONE,
        Problem\UnprocessableRequest::class => StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
        Problem\ServiceUnavailable::class => StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE,
    ];

    /** @param array<string, int> $conversionMap */
    public function __construct(private array $conversionMap = self::DEFAULT_MAP)
    {
    }

    public function extractStatusCode(Throwable $error): int
    {
        foreach ($this->conversionMap as $class => $code) {
            if ($error instanceof $class) {
                return $code;
            }
        }

        $code = $error->getCode();
        assert(is_int($code));

        return $code !== 0 ? $code : StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
    }
}
