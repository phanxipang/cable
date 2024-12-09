<?php

declare(strict_types=1);

namespace Fansipan\Cable\Event;

use Fansipan\Cable\State\State;

final class PlanCreated
{
    public function __construct(
        public readonly State $state,
    ) {
    }
}
