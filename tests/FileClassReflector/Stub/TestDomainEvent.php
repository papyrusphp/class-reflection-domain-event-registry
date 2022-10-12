<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub;

use Papyrus\EventSourcing\DomainEvent;

final class TestDomainEvent implements DomainEvent
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
