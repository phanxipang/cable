<?php

declare(strict_types=1);

namespace Fansipan\Cable\Exception;

use Fansipan\Cable\State\Resource;

class ChecksumVerifyFailedException extends \LogicException
{
    public function __construct(
        public readonly Resource $resource,
        string $message = 'Checksum is invalid',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
