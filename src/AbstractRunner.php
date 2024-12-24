<?php

declare(strict_types=1);

namespace Fansipan\Cable;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use Fansipan\Cable\Serialization\JsonSerializer;
use Fansipan\Cable\Serialization\Serializer;
use Fansipan\Cable\State\PlanCollection;
use Fansipan\Cable\State\State;
use League\Flysystem\FilesystemOperator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
abstract class AbstractRunner implements Runner
{
    protected readonly LoggerInterface $logger;

    protected readonly Serializer $serializer;

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    public function __construct(
        protected readonly Runner $runner,
        protected readonly FilesystemOperator $storage,
        ?LoggerInterface $logger = null,
        ?Serializer $serializer = null,
        protected readonly ?EventDispatcherInterface $event = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->serializer = $serializer ?? new JsonSerializer();
    }

    /**
     * @param  array<string, mixed> $options
     */
    public function withOptions(array $options): static
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $clone = clone $this;
        $clone->options = $resolver->resolve($options);

        return $clone;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        //
    }

    protected function hasState(): bool
    {
        return $this->storage->fileExists(State::STATE_FILE);
    }

    protected function readState(): State
    {
        if ($this->hasState()) {
            $output = State::STATE_FILE;
            $this->logger->notice('State file exists. Creating back up file.');
            $this->storage->copy($output, $output.sprintf('.%d.backup', \time()));

            $mapper = (new MapperBuilder())
                ->allowSuperfluousKeys()
                ->allowPermissiveTypes()
                ->enableFlexibleCasting()
                ->registerConstructor(Uuid::fromString(...))
                ->mapper();

            $state = $mapper->map(State::class, Source::json($this->storage->read($output)));
        } else {
            $state = new State(new \DateTimeImmutable(), new PlanCollection());
        }

        return $state;
    }

    protected function writeState(State $state): void
    {
        $normalizer = (new MapperBuilder())
            ->registerTransformer(static fn (\JsonSerializable $object, callable $next) => $object->jsonSerialize()) // @phpstan-ignore argument.type
            ->normalizer(Format::json());
        // ->streamTo(State::STATE_FILE);

        // $this->storage->writeStream(State::STATE_FILE, $normalizer->normalize($state));
        $this->storage->write(State::STATE_FILE, $normalizer->normalize($state));
    }
}
