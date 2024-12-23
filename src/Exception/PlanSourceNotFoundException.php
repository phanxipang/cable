<?php

declare(strict_types=1);

namespace Fansipan\Cable\Exception;

use Fansipan\Cable\State\Plan;

class PlanSourceNotFoundException extends \RuntimeException
{
    public function __construct(
        public readonly Plan $resource,
        string $message = 'Data source not found.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
