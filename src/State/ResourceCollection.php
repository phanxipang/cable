<?php

declare(strict_types=1);

namespace Fansipan\Cable\State;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Resource>
 */
final class ResourceCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Resource::class;
    }

    public function has(Resource $resource): bool
    {
        return $this->filter(fn (Resource $item) => $this->equals($item->checksum, $resource->checksum))
            ->count() > 0;
    }

    private function equals(string|\Stringable $a, string|\Stringable $b): bool
    {
        return (string) $a === (string) $b;
    }
}
