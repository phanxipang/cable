<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\Event\PlanCreated;
use Fansipan\Cable\State\Envelope;
use Fansipan\Cable\State\State;
use ScriptFUSION\Porter\Collection\RecordCollection;
use ScriptFUSION\Porter\Import\Import;

final class Planner extends AbstractRunner
{
    public function source(): Import
    {
        return $this->runner->source();
    }

    public function handle(RecordCollection $data, Acknowledger $ack): void
    // public function handle(RecordCollection $data): void
    {
        $output = $this->outputFilename();

        if ($this->storage->fileExists($output)) {
            $this->logger->notice('State file exists. Creating back up file.');
            $this->storage->copy($output, $output.sprintf('.%d.backup', \time()));
        }

        $this->logger->info('Writing state file');

        $state = new State(new \DateTimeImmutable(), $data);
        $envelope = new Envelope($state);

        $this->storage->write($output, $this->serializer->encode($envelope));

        $this->event?->dispatch(new PlanCreated($state));

        $this->logger->info('Plan has been created successfully', compact('envelope'));
    }
}
