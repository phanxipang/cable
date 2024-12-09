<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use ScriptFUSION\Porter\Collection\RecordCollection;
use ScriptFUSION\Porter\Import\Import;

interface Runner
{
    public function source(): Import;

    public function handle(RecordCollection $data, Acknowledger $ack): void;
    // public function handle(RecordCollection $data): void;
}
