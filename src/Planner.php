<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\Event\PlanCreated;
use Fansipan\Cable\State\Plan;
use Ramsey\Uuid\Rfc4122\UuidV7;
use ScriptFUSION\Porter\Collection\RecordCollection;
use ScriptFUSION\Porter\Import\Import;

final class Planner extends AbstractRunner
{
    private ?string $output = null;

    public function source(): Import
    {
        return $this->runner->source();
    }

    public function output(string $filename): self
    {
        $this->output = $filename;

        return $this;
    }

    public function handle(RecordCollection $data, Acknowledger $ack): void
    // public function handle(RecordCollection $data): void
    {
        $state = $this->readState();

        $this->logger->info('Writing state file');

        if (\is_null($this->output)) {
            $this->logger->warning('You didn\'t specify the output option to save this plan, there is no guarantee that the same exact data will be used for the next run.');
        }

        $state->time = new \DateTimeImmutable();
        $out = $this->output ?? sprintf('plan-%d', $state->time->getTimestamp());

        $this->storage->write($out, $this->serializer->encode($data));

        $plan = new Plan(
            UuidV7::fromDateTime($state->time),
            $out,
            'json',
            $this->storage->checksum($out),
        );

        if (! $state->plans->has($plan)) {
            $state->plans->add($plan);
        }

        $this->writeState($state);

        $this->event?->dispatch(new PlanCreated($state));

        $this->logger->info('Plan has been created successfully', compact('state'));
    }
}
