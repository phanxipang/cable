<?php

declare(strict_types=1);

namespace Fansipan\Cable\State\Serialization;

use Fansipan\Cable\State\Envelope;

interface Serializer
{
    public function decode(string $encoded): Envelope;

    public function encode(Envelope $envelope): string;
}
