<?php

declare(strict_types=1);

namespace Fansipan\Cable\Serialization;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\Normalizer\Normalizer;

final class JsonSerializer implements Serializer
{
    /**
     * @var Normalizer<string>
     */
    private readonly Normalizer $normalizer;

    /**
     * @param  ?Normalizer<string> $normalizer
     */
    public function __construct(?Normalizer $normalizer = null)
    {
        $this->normalizer = $normalizer ?? (new MapperBuilder())
            ->normalizer(Format::json());
    }

    public function decode(string $encoded): iterable
    {
        return \json_decode($encoded, true);
    }

    public function encode(iterable $data): string
    {
        return $this->normalizer->normalize($data);
    }
}
