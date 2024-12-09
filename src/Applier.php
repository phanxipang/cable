<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\Event\PlanApplied;
use Fansipan\Cable\Exception\StateNotFoundException;
use ScriptFUSION\Porter\Collection\RecordCollection;
use ScriptFUSION\Porter\Import\Import;
use ScriptFUSION\Porter\Import\StaticImport;

final class Applier extends AbstractRunner
{
    public function source(): Import
    {
        $output = $this->outputFilename();

        if (! $this->storage->fileExists($output)) {
            throw new StateNotFoundException('State does not exists');
        }

        $state = $this->serializer->decode($this->storage->read($output))->state;

        $data = static fn () => yield from $state->data;

        return new StaticImport($data());
    }

    public function handle(RecordCollection $data, Acknowledger $ack): void
    // public function handle(RecordCollection $data): void
    {
        $this->runner->handle($data, $ack);

        $this->event?->dispatch(new PlanApplied());

        $this->logger->info('Plan has been applied successfully');
    }
}
