<?php
declare(strict_types=1);

namespace Lcobucci\ErrorHandling\StatusCodeExtractionStrategy;

use Fig\Http\Message\StatusCodeInterface;
use Lcobucci\ErrorHandling\Problem;
use Lcobucci\ErrorHandling\StatusCodeExtractionStrategy;
use Throwable;

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

    /**
     * @var array<string, int>
     */
    private array $conversionMap;

    /**
     * @param array<string, int> $conversionMap
     */
    public function __construct(array $conversionMap = self::DEFAULT_MAP)
    {
        $this->conversionMap = $conversionMap;
    }

    public function extractStatusCode(Throwable $error): int
    {
        foreach ($this->conversionMap as $class => $code) {
            if ($error instanceof $class) {
                return $code;
            }
        }

        $code = $error->getCode();

        return $code !== 0 ? $code : StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
    }
}
