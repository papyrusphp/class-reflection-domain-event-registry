<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry;

use Papyrus\DomainEventRegistry\DomainEventNotRegisteredException;
use Papyrus\DomainEventRegistry\DomainEventRegistry;
use Papyrus\EventSourcing\DomainEvent;

final class ClassReflectionDomainEventRegistry implements DomainEventRegistry
{
    /**
     * @var array<string, class-string<DomainEvent>>
     */
    private array $domainEventClassNames = [];

    public function __construct(
        private readonly DomainEventClassNameLoader $domainEventClassNameLoader,
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
            if (is_subclass_of($class, DomainEvent::class)) {
                $this->domainEventClassNames[$class::getEventName()] = $class;
            }
        }
    }
}
