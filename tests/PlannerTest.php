<?php

declare(strict_types=1);

namespace Fansipan\Cable\Tests;

use Fansipan\Cable\Acknowledger;
use Fansipan\Cable\Planner;
use Fansipan\Cable\State\State;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use ScriptFUSION\Porter\Porter;

final class PlannerTest extends TestCase
{
    public function test_planner(): void
    {
        $porter = new Porter($this->createContainer());
        $importer = JsonData::createFromFile(__DIR__.'/fixtures/posts.json');
        $planner = new Planner($importer, $storage = new Filesystem(new InMemoryFilesystemAdapter()));

        $planner->handle($porter->import($planner->source()), new Acknowledger());

        $this->assertTrue($storage->has(State::STATE_FILE));
    }
}
