# 📜 Papyrus Domain event registry: File class reflection implementation
[![Build Status](https://scrutinizer-ci.com/g/papyrusphp/class-reflection-domain-event-registry/badges/build.png?b=main)](https://github.com/papyrusphp/class-reflection-domain-event-registry/actions)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/papyrusphp/class-reflection-domain-event-registry.svg?style=flat)](https://scrutinizer-ci.com/g/papyrusphp/class-reflection-domain-event-registry/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/papyrusphp/class-reflection-domain-event-registry.svg?style=flat)](https://scrutinizer-ci.com/g/papyrusphp/class-reflection-domain-event-registry)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/papyrus/class-reflection-domain-event-registry.svg?style=flat&include_prereleases)](https://packagist.org/packages/papyrus/class-reflection-domain-event-registry)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg?style=flat)](http://www.php.net)

Implementation of [papyrus/domain-event-registry](https://github.com/papyrusphp/domain-event-registry), based on class reflection

## Installation
Install via composer:
```bash
composer require papyrus/class-reflection-domain-event-registry
```

## Configuration
Bind your own implementation to the interface `DomainEventRegistry` in your service definitions, e.g.:

A plain PHP PSR-11 Container definition:

```php
use Jerowork\FileClassReflector\ClassReflectorFactory;
use Papyrus\ClassReflectionDomainEventRegistry\Cached\CachedDomainEventClassNameLoaderDecorator;
use Papyrus\ClassReflectionDomainEventRegistry\ClassReflectionDomainEventRegistry;
use Papyrus\ClassReflectionDomainEventRegistry\DomainEventClassNameLoader;
use Papyrus\ClassReflectionDomainEventRegistry\FileClassReflector\FileClassReflectorDomainEventClassNameLoader;
use Papyrus\DomainEventRegistry\DomainEventRegistry;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

return [
    // Other definitions
    // ...

    DomainEventRegistry::class => static function (ContainerInterface $container): DomainEventRegistry {
        // Optionally wrapped in a cache (PSR-16) layer (recommended)
        return new CachedDomainEventClassNameLoaderDecorator(
            new ClassReflectionDomainEventRegistry(
                $container->get(DomainEventClassNameLoader::class),
                '/some-path/to/Domain' // Your path to your Domain layer, where the domain events reside
            ),
            // A cache implementation of your choice
            $container->get(CacheInterface::class),
        );
    },
    
    DomainEventClassNameLoader::class => static function (ContainerInterface $container): DomainEventClassNameLoader {
        return new FileClassReflectorDomainEventClassNameLoader(
            // See jerowork/file-class-reflector for DI definitions
            $container->get(ClassReflectorFactory::class),
        );    
    },
];
```
A Symfony YAML-file definition:
```yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true

  # Other definitions
  # ...

  Papyrus\DomainEventRegistry\DomainEventRegistry:
    class: Papyrus\ClassReflectionDomainEventRegistry\ClassReflectionDomainEventRegistry

  # Optionally wrapped in a cache (PSR-16) layer (recommended)
  Papyrus\ClassReflectionDomainEventRegistry\Cached\CachedDomainEventClassNameLoaderDecorator: ~
    decorates: '@Papyrus\DomainEventRegistry\DomainEventRegistry'

  Papyrus\ClassReflectionDomainEventRegistry\DomainEventClassNameLoader:
    class: Papyrus\ClassReflectionDomainEventRegistry\FileClassReflector\FileClassReflectorDomainEventClassNameLoader
```
