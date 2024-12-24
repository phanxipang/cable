<?php

declare(strict_types=1);

namespace Fansipan\Cable\State;

use Ramsey\Uuid\UuidInterface;

final class Plan
{
    /**
     * @param  array<array-key, mixed> $meta
     */
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $source,
        public readonly string $checksum,
        public readonly array $meta = [],
    ) {
    }
}
