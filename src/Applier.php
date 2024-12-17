<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\Event\PlanApplied;
use Fansipan\Cable\Exception\ChecksumVerifyFailedException;
use Fansipan\Cable\Exception\ResourceSourceNotFoundException;
use Fansipan\Cable\State\Resource;
use Fansipan\Cable\State\State;
use Ramsey\Collection\Exception\NoSuchElementException;
use ScriptFUSION\Porter\Collection\PorterRecords;
use ScriptFUSION\Porter\Collection\RecordCollection;
use ScriptFUSION\Porter\Connector\ImportConnector;
use ScriptFUSION\Porter\Import\Import;
use ScriptFUSION\Porter\Provider\Resource\ProviderResource;
use ScriptFUSION\Porter\Provider\StaticDataProvider;

final class Applier extends AbstractRunner implements ProviderResource
{
    private Resource $resource;

    public function getProviderClassName(): string
    {
        return StaticDataProvider::class;
    }

    public function fetch(ImportConnector $connector): \Iterator
    {
        $output = State::STATE_FILE;

        if (! $this->storage->fileExists($output)) {
            $this->logger->warning(sprintf('State file not found. Using the original source from %s.', \get_debug_type($this->runner)));

            return $this->runner->source();
        }

        $resource = $this->getResource();

        // if (! $resource) {
        //     throw new \InvalidArgumentException('State doesn\'t contain any resources.');
        // }

        $source = $resource->source;

        if (! $this->storage->fileExists($source)) {
            throw new ResourceSourceNotFoundException('Resource source not found.');
        }

        if ($this->storage->checksum($source) !== $resource->checksum) {
            throw new ChecksumVerifyFailedException($resource);
        }

        yield from $this->serializer->decode($this->storage->read($source));
    }

    public function source(): Import
    {
        return new Import($this);
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

    private function getResource(): Resource
    {
        if (! isset($this->resource)) {
            $state = $this->readState();

            $this->resource = $state->resources->last();
        }

        return $this->resource;
    }

    private function removeResource(): void
    {
        try {
            $resource = $this->getResource();
            $state = $this->readState();

            // @phpstan-ignore assign.propertyType
            $state->resources = $state->resources->filter(static fn (Resource $item) => ! $item->id->equals($resource->id));
            $this->storage->delete($resource->source);
            $this->writeState($state);
        } catch (NoSuchElementException) {
            return;
        }
    }
}
