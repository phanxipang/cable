<?php

declare(strict_types=1);

namespace Fansipan\Cable\Event;

use Fansipan\Cable\State\Plan;
use Fansipan\Cable\State\State;

final class PlanCreated
{
    public function __construct(
        public readonly Plan $plan,
        public readonly State $state,
    ) {
    }
}
