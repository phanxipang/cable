<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Amp\Pipeline\Pipeline;
use ScriptFUSION\Porter\Collection\RecordCollection;

trait ConcurrentHandler
{
    private function concurrency(): int
    {
        return 20;
    }

    public function handle(RecordCollection $data, Acknowledger $ack): void
    // public function handle(RecordCollection $data): void
    {
        Pipeline::fromIterable($data)
            ->concurrent($this->concurrency())
            // ->forEach($this->process(...));
            ->forEach(fn (mixed $item) => $this->process($item, $ack));
    }

    abstract private function process(mixed $data, Acknowledger $ack): void;
}
