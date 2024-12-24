<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\Event\PlanApplied;
use Fansipan\Cable\Exception\InvalidPlanException;
use Fansipan\Cable\Exception\PlanChecksumVerifyFailedException;
use Fansipan\Cable\Exception\PlanSourceNotFoundException;
use Fansipan\Cable\Exception\StateNotFoundException;
use Fansipan\Cable\State\Plan;
use Fansipan\Cable\State\State;
use Ramsey\Collection\Exception\NoSuchElementException;
use Ramsey\Uuid\Rfc4122\UuidV7;
use Ramsey\Uuid\UuidInterface;
use ScriptFUSION\Porter\Collection\PorterRecords;
use ScriptFUSION\Porter\Collection\RecordCollection;
use ScriptFUSION\Porter\Connector\ImportConnector;
use ScriptFUSION\Porter\Import\Import;
use ScriptFUSION\Porter\Provider\Resource\ProviderResource;
use ScriptFUSION\Porter\Provider\StaticDataProvider;

final class Applier extends AbstractRunner implements ProviderResource
{
    private ?UuidInterface $id = null;

    private Plan $plan;

    public function setPlanId(string|UuidInterface $id): self
    {
        $this->id = $id instanceof UuidInterface ? $id : UuidV7::fromString($id);

        return $this;
    }

    public function getProviderClassName(): string
    {
        return StaticDataProvider::class;
    }

    public function fetch(ImportConnector $connector): \Iterator
    {
        if (! $this->hasState()) {
            throw new StateNotFoundException('State file not found. Unable to fetch data.');
        }

        try {
            $plan = $this->getPlan();
        } catch (NoSuchElementException $e) {
            throw $this->id ? InvalidPlanException::notFound($this->id, $e) : InvalidPlanException::empty($e);
        }

        $source = $plan->source;

        if (! $this->storage->fileExists($source)) {
            throw new PlanSourceNotFoundException($plan);
        }

        if ($this->storage->checksum($source) !== $plan->checksum) {
            throw new PlanChecksumVerifyFailedException($plan);
        }

        yield from $this->serializer->decode($this->storage->read($source));
    }

    public function source(): Import
    {
        // if ($this->stateFileExists()) {
        return new Import($this);
        // }

        // $this->logger->warning(sprintf('State file not found. Using the original source from %s.', \get_debug_type($this->runner)));

        // return $this->runner->source();
    }

    public function handle(RecordCollection $data, Acknowledger $ack): void
    // public function handle(RecordCollection $data): void
    {
        $this->runner->handle($data, $ack);

        $this->event?->dispatch(new PlanApplied());

        $this->logger->info('Plan has been applied successfully');

        if ($data instanceof PorterRecords) {
            $this->removeResource();
        }
    }

    private function getPlan(): Plan
    {
        if (! isset($this->plan)) {
            $state = $this->readState();

            $this->plan = $this->id
                ? $state->plans->find($this->id)
                : $state->plans->last();
        }

        return $this->plan;
    }

    private function removeResource(): void
    {
        try {
            $plan = $this->getPlan();
            $state = $this->readState();

            // @phpstan-ignore assign.propertyType
            $state->plans = $state->plans->filter(static fn (Plan $item) => ! $item->id->equals($plan->id));
            $this->storage->delete($plan->source);
            $this->writeState($state);
        } catch (NoSuchElementException) {
            $this->logger->warning('Unable to remove plan entry. Clean up operation is not completed.');

            return;
        }
    }
}
