<?php

declare(strict_types=1);

namespace Fansipan\Cable\State;

use Ramsey\Collection\AbstractCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends AbstractCollection<Plan>
 */
final class PlanCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Plan::class;
    }

    public function has(Plan $plan): bool
    {
        return $this->filter(static fn (Plan $item) => $item->checksum === $plan->checksum)
            ->count() > 0;
    }

    public function find(UuidInterface $id): Plan
    {
        return $this->filter(static fn (Plan $item) => $item->id->equals($id))
            ->first();
    }
}
