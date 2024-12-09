<?php

declare(strict_types=1);

namespace Fansipan\Cable;

class Acknowledger
{
    private \Closure $ack;

    /**
     * @param callable(mixed, \Throwable|null):void|null $ack
     */
    public function __construct(
        ?callable $ack = null,
    ) {
        $this->ack = $ack ? \Closure::fromCallable($ack) : static function () {};
    }

    public function ack(mixed $result = null): void
    {
        ($this->ack)($result);
    }

    public function nack(\Throwable $error): void
    {
        ($this->ack)(null, $error);
    }

    // public function __destruct()
    // {
    //     // if (null !== $this->ack) {
    //     //     throw new \LogicException('The acknowledger was not called by the handler.');
    //     // }
    //     $this->ack = null;
    // }

    // private function doAck(?\Throwable $e = null, mixed $result = null): void
    // {
    //     if (! $ack = $this->ack) {
    //         throw new \LogicException('The acknowledger cannot be called twice by the handler.');
    //     }
    //     $this->ack = null;
    //     $this->error = $e;
    //     $this->result = $result;
    //     $ack($e, $result);
    // }
}
