<?php

declare(strict_types=1);

namespace Fansipan\Cable\State\Serialization;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use Fansipan\Cable\Exception\StateDecodingFailedException;
use Fansipan\Cable\State\Envelope;
use Fansipan\Cable\State\Stamp\StampBag;

final class ValinorSerializer implements Serializer
{
    public function decode(string $encoded): Envelope
    {
        $encodedEnvelope = \json_decode($encoded, true, \JSON_THROW_ON_ERROR);

        if (empty($encodedEnvelope['state'])) {
            throw new StateDecodingFailedException('Encoded envelope should have at least a "state", or maybe you should implement your own serializer.');
        }

        $builder = (new MapperBuilder())
            ->allowSuperfluousKeys()
            ->enableFlexibleCasting();

        $stamps = [];

        foreach ($encodedEnvelope['stamps'] ?? [] as $fqcn => $stamp) {
            $stamps[] = $builder->mapper()->map($fqcn, $stamp);
        }

        $stamps = new StampBag($stamps);

        return $builder->allowPermissiveTypes()
            ->mapper()
            ->map(Envelope::class, [
                'stamps' => $stamps,
                'state' => $encodedEnvelope['state'],
            ]);
    }

    public function encode(Envelope $envelope): string
    {
        return (new MapperBuilder())
            ->normalizer(Format::json())
            ->normalize($envelope);
    }
}
