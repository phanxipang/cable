<?php

declare(strict_types=1);

namespace Fansipan\Cable\Tests;

use Fansipan\Cable\Acknowledger;
use Fansipan\Cable\ConcurrentHandler;
use Fansipan\Cable\Runner;
use ScriptFUSION\Porter\Collection\CountableProviderRecords;
use ScriptFUSION\Porter\Import\Import;
use ScriptFUSION\Porter\Provider\Resource\StaticResource;

final class JsonData extends StaticResource implements Runner
{
    use ConcurrentHandler;

    private InMemoryData $storage;

    public function __construct(
        string|array $data,
    ) {
        $this->storage = new InMemoryData();
        $data = \is_array($data) ? $data : \json_decode($data, true, \JSON_THROW_ON_ERROR);
        $gen = static fn () => yield from $data;

        parent::__construct(new CountableProviderRecords($gen(), \count($data), $this));
    }

    public function source(): Import
    {
        return new Import($this);
    }

    private function process(mixed $data, Acknowledger $ack): void
    {
        $this->storage->add($data);
        // \usleep(3000);
        $ack->ack($data);
    }

    public function storage(): InMemoryData
    {
        return $this->storage;
    }

    public static function createFromFile(string $path): self
    {
        return new self(\file_get_contents($path));
    }
}
