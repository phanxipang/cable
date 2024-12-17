<?php

declare(strict_types=1);

namespace Fansipan\Cable\State;

final class State
{
    public const STATE_FILE = 'cable.state';

    public function __construct(
        public \DateTimeInterface $time,
        public ResourceCollection $resources,
    ) {
    }
}
