<?php

declare(strict_types=1);

namespace Fansipan\Cable\State\Stamp;

/**
 * @implements \IteratorAggregate<class-string<Stamp>, Stamp>
 */
final class StampBag implements \IteratorAggregate, \Countable
{
    /**
     * @var array<class-string<Stamp>, Stamp>
     */
    private array $stamps = [];

    /**
     * @param  iterable<Stamp> $stamps
     */
    public function __construct(
        iterable $stamps = [],
    ) {
        foreach ($stamps as $stamp) {
            $this->stamps[$stamp::class] = $stamp;
        }
    }

    /**
     * @return \ArrayIterator<class-string<Stamp>, Stamp>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->stamps);
    }

    public function count(): int
    {
        return \count($this->stamps);
    }

    public function with(Stamp ...$stamps): static
    {
        $cloned = clone $this;

        foreach ($stamps as $stamp) {
            $cloned->stamps[$stamp::class] = $stamp;
        }

        return $cloned;
    }

    /**
     * @template T of Stamp
     *
     * @param  class-string<T> $stamp
     * @return ?T
     */
    public function get(string $stamp): ?Stamp
    {
        // @phpstan-ignore return.type
        return $this->stamps[$stamp] ?? null;
    }

    /**
     * @return array<class-string<Stamp>, Stamp>
     */
    public function all(): array
    {
        return $this->stamps;
    }
}
