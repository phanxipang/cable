<?php

declare(strict_types=1);

namespace Fansipan\Cable\Tests;

use Fansipan\Cable\Acknowledger;
use Fansipan\Cable\Applier;
use Fansipan\Cable\Planner;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use ScriptFUSION\Porter\Porter;

final class ApplyImporterTest extends TestCase
{
    public function test_applier(): void
    {
        $porter = new Porter($this->createContainer());
        $importer = JsonData::createFromFile(__DIR__.'/fixtures/posts.json');
        $planner = new Planner($importer, $storage = new Filesystem(new InMemoryFilesystemAdapter()));

        $planner->handle($porter->import($planner->source()), new Acknowledger());

        $applier = new Applier($importer, $storage);
        $applier->handle($porter->import($applier->source()), new Acknowledger());

        $this->assertCount(100, $importer->storage());
    }
}
