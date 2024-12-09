<?php

declare(strict_types=1);

namespace Fansipan\Cable\State\Stamp;

final class IteratorImplementationStamp implements Stamp
{
    public function __construct(
        public readonly string $fqcn,
    ) {
    }
}
