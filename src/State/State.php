<?php

declare(strict_types=1);

namespace Fansipan\Cable\State;

final class State
{
    public const STATE_FILE = 'cable.state';

    /**
     * @param iterable<array-key, mixed> $data
     */
    public function __construct(
        public readonly \DateTimeInterface $time,
        public readonly iterable $data,
    ) {
    }
}
