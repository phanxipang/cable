<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use Fansipan\Cable\State\Serialization\Serializer;
use Fansipan\Cable\State\Serialization\ValinorSerializer;
use Fansipan\Cable\State\State;
use League\Flysystem\FilesystemOperator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class AbstractRunner implements Runner
{
    protected readonly Serializer $serializer;

    protected readonly LoggerInterface $logger;

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    public function __construct(
        protected readonly Runner $runner,
        protected readonly FilesystemOperator $storage,
        ?LoggerInterface $logger = null,
        protected readonly ?EventDispatcherInterface $event = null,
        ?Serializer $serializer = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->serializer = $serializer ?? new ValinorSerializer();
    }

    /**
     * @param  array<string, mixed> $options
     */
    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->options = $options;

        return $clone;
    }

    public function withOption(string $key, mixed $value): static
    {
        $clone = clone $this;
        $clone->options[$key] = $value;

        return $clone;
    }

    protected function outputFilename(): string
    {
        return $this->options['output'] ?? State::STATE_FILE;
    }
}
