<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub;

final class TestDomainEvent
{
    public static function getEventName(): string
    {
        return 'test.domain_event';
    }
}
