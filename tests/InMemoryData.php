<?php

declare(strict_types=1);

namespace Fansipan\Cable\Tests;

final class InMemoryData implements \IteratorAggregate, \Countable
{
    public function __construct(
        private array $data = [],
    ) {
    }

    public function add(mixed $data): self
    {
        $this->data[] = $data;

        return $this;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return \count($this->data);
    }
}
