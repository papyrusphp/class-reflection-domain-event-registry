<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\FileClassReflector;

use Jerowork\FileClassReflector\ClassReflectorFactory;
use Papyrus\ClassReflectionDomainEventRegistry\DomainEventClassNameLoader;

/**
 * @template DomainEvent of object
 *
 * @implements DomainEventClassNameLoader<DomainEvent>
 */
final class FileClassReflectorDomainEventClassNameLoader implements DomainEventClassNameLoader
{
    public function __construct(
        private readonly ClassReflectorFactory $classReflectorFactory,
    ) {
    }

    public function load(string $directory): array
    {
        $reflector = $this->classReflectorFactory->create()->addDirectory($directory);
        $domainEventClassNames = [];
        foreach ($reflector->reflect()->getClasses() as $class) {
            if (str_ends_with($class->getName(), 'Event') === false) {
                continue;
            }

            /** @var class-string<DomainEvent> $domainEventClassName */
            $domainEventClassName = $class->getName();
            $domainEventClassNames[] = $domainEventClassName;
        }

        return $domainEventClassNames;
    }
}
