<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\Event\PlanCreated;
use Fansipan\Cable\State\Envelope;
use Fansipan\Cable\State\Resource;
use Ramsey\Uuid\Rfc4122\UuidV7;
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
        $state = $this->readState();

        $this->logger->info('Writing state file');

        $out = 'out';

        $this->storage->write($out, $this->serializer->encode($data));

        $state->time = new \DateTimeImmutable();
        $resource = new Resource(
            UuidV7::fromDateTime($state->time),
            $out,
            'json',
            $this->storage->checksum($out),
        );

        if (! $state->resources->has($resource)) {
            $state->resources->add($resource);
        }

        // $envelope = new Envelope($state);

        $this->writeState($state);

        $this->event?->dispatch(new PlanCreated($state));

        $this->logger->info('Plan has been created successfully', compact('state'));
    }
}
