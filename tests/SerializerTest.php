<?php

declare(strict_types=1);

namespace Fansipan\Cable\Tests;

use Defuse\Crypto\Key;
use Defuse\Crypto\KeyProtectedByPassword;
use Fansipan\Cable\Serialization\JsonSerializer;
use Fansipan\Cable\Serialization\SymmetricEncryptionSerializer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SerializerTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_json_serializer(iterable $data): void
    {
        $serializer = new JsonSerializer();

        $this->assertJson($json = $serializer->encode($data));
        $this->assertEquals($data, $serializer->decode($json));
    }

    #[DataProvider('provideData')]
    public function test_symmetric_encryption_serializer_using_key(iterable $data): void
    {
        $serializer = new SymmetricEncryptionSerializer(new JsonSerializer(), Key::createNewRandomKey());
        $text = $serializer->encode($data);

        $this->assertStringStartsNotWith('{', $text);
        $this->assertStringStartsNotWith('}', $text);
        $this->assertStringStartsNotWith('[', $text);
        $this->assertStringStartsNotWith(']', $text);
        $this->assertEquals($data, $serializer->decode($text));
    }

    #[DataProvider('provideData')]
    public function test_symmetric_encryption_serializer_using_key_protected_by_password(iterable $data): void
    {
        $password = 'zBhBifFr5nhy6eEH';
        $key = KeyProtectedByPassword::createRandomPasswordProtectedKey($password);

        $this->expectException(\InvalidArgumentException::class);
        new SymmetricEncryptionSerializer(new JsonSerializer(), $key);

        $serializer = new SymmetricEncryptionSerializer(new JsonSerializer(), $key, $password);
        $text = $serializer->encode($data);

        $this->assertStringStartsNotWith('{', $text);
        $this->assertStringStartsNotWith('}', $text);
        $this->assertStringStartsNotWith('[', $text);
        $this->assertStringStartsNotWith(']', $text);
        $this->assertEquals($data, $serializer->decode($text));
    }

    public static function provideData(): iterable
    {
        yield [['string' => 'foo', 'bool' => true, 'int' => 1]];
        yield [['1', 1, true, 'true', 'on']];
    }
}
