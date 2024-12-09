<?php

declare(strict_types=1);

namespace Fansipan\Cable;

interface RunnerFactory
{
    /**
     * Create new importer by given id.
     *
     * @throws \Fansipan\Cable\Exception\RunnerNotFoundException if the id is invalid
     */
    public function create(string $id): Runner;
}
