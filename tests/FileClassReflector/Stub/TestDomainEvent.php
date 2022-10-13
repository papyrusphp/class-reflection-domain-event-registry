<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub;

use Papyrus\DomainEventRegistry\DomainEventNameResolver\NamedDomainEvent;
use Papyrus\EventSourcing\DomainEvent;

final class TestDomainEvent implements DomainEvent, NamedDomainEvent
{
    public static function getEventName(): string
    {
        return 'test.domain_event';
    }

    public function getAggregateRootId(): string
    {
        return '';
    }
}
