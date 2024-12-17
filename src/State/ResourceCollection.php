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
        return $this->filter(fn (Resource $item) => $item->checksum === $resource->checksum)
            ->count() > 0;
    }
}
