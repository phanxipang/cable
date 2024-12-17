<?php

declare(strict_types=1);

namespace Fansipan\Cable\Serialization;

interface Serializer
{
    /**
     * Decode the encoded data.
     *
     * @return  iterable<mixed>
     */
    public function decode(string $encoded): iterable;

    /**
     * Encode the data.
     *
     * @param  iterable<mixed> $data
     */
    public function encode(iterable $data): string;
}
