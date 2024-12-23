<?php

declare(strict_types=1);

namespace Fansipan\Cable\Exception;

use Ramsey\Uuid\UuidInterface;

class InvalidPlanException extends \InvalidArgumentException
{
    public static function notFound(UuidInterface $id, ?\Throwable $previous = null): self
    {
        return new self(sprintf('Invalid plan with ID "%s".', $id), $previous?->getCode() ?? 0, $previous);
    }

    public static function empty(?\Throwable $previous = null): self
    {
        return new self('No plans found.', $previous?->getCode() ?? 0, $previous);
    }
}
