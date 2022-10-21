<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry;

use Papyrus\DomainEventRegistry\DomainEventNameResolver\DomainEventNameResolver;
use Papyrus\DomainEventRegistry\DomainEventNotRegisteredException;
use Papyrus\DomainEventRegistry\DomainEventRegistry;

/**
 * @template DomainEvent of object
 *
 * @implements DomainEventRegistry<DomainEvent>
 */
final class ClassReflectionDomainEventRegistry implements DomainEventRegistry
{
    /**
     * @var array<string, class-string<DomainEvent>>
     */
    private array $domainEventClassNames = [];

    /**
     * @param DomainEventClassNameLoader<DomainEvent> $domainEventClassNameLoader
     * @param DomainEventNameResolver<DomainEvent> $domainEventNameResolver
     */
    public function __construct(
        private readonly DomainEventClassNameLoader $domainEventClassNameLoader,
        private readonly DomainEventNameResolver $domainEventNameResolver,
        private readonly string $directory,
    ) {
    }

    public function retrieve(string $eventName): string
    {
        if (count($this->domainEventClassNames) === 0) {
            $this->loadClassNames();
        }

        if (array_key_exists($eventName, $this->domainEventClassNames) === false) {
            throw DomainEventNotRegisteredException::withEventName($eventName);
        }

        return $this->domainEventClassNames[$eventName];
    }

    private function loadClassNames(): void
    {
        $classes = $this->domainEventClassNameLoader->load($this->directory);

        foreach ($classes as $class) {
            $this->domainEventClassNames[$this->domainEventNameResolver->resolve($class)] = $class;
        }
    }
}
