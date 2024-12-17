<?php

declare(strict_types=1);

namespace Fansipan\Cable\Serialization;

use Defuse\Crypto\Core;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\KeyProtectedByPassword;

if (! \class_exists(Core::class)) {
    throw new \LogicException('You cannot use the "Fansipan\Cable\Serialization\SymmetricEncryptionSerializer" as the "defuse/php-encryption" package is not installed. Try running "composer require defuse/php-encryption".');
}

final class SymmetricEncryptionSerializer implements Serializer
{
    private readonly Key $key;

    public function __construct(
        private readonly Serializer $serializer,
        #[\SensitiveParameter]
        Key|KeyProtectedByPassword $key,
        #[\SensitiveParameter]
        ?string $password = null,
    ) {
        if ($key instanceof KeyProtectedByPassword) {
            if (! $password) {
                throw new \InvalidArgumentException('$password must not be empty when using KeyProtectedByPassword');
            }

            $key = $key->unlockKey($password);
        }

        $this->key = $key;
    }

    public function decode(string $encoded): iterable
    {
        return $this->serializer->decode(Crypto::decrypt($encoded, $this->key));
    }

    public function encode(iterable $data): string
    {
        return Crypto::encrypt($this->serializer->encode($data), $this->key);
    }
}
