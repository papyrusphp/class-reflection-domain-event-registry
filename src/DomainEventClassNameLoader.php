<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry;

use Papyrus\EventSourcing\DomainEvent;

interface DomainEventClassNameLoader
{
    /**
     * @return list<class-string<DomainEvent>>
     */
    public function load(string $directory): array;
}
