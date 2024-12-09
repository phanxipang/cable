<?php

declare(strict_types=1);

namespace Fansipan\Cable\State;

use Fansipan\Cable\State\Stamp\Stamp;
use Fansipan\Cable\State\Stamp\StampBag;

final class Envelope
{
    private StampBag $stamps;

    /**
     * @param  StampBag|iterable<Stamp> $stamps
     */
    public function __construct(
        public readonly State $state,
        StampBag|iterable $stamps = [],
    ) {
        $this->stamps = $stamps instanceof StampBag ? $stamps : new StampBag($stamps);
    }

    /**
     * Adds one or more stamps.
     */
    public function with(Stamp ...$stamps): static
    {
        $cloned = clone $this;

        $cloned->stamps = $cloned->stamps->with(...$stamps);

        return $cloned;
    }
}
