<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry;

/**
 * @template DomainEvent of object
 */
interface DomainEventClassNameLoader
{
    /**
     * @return list<class-string<DomainEvent>>
     */
    public function load(string $directory): array;
}
