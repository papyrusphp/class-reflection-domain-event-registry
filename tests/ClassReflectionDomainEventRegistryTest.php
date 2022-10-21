<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Test;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Papyrus\ClassReflectionDomainEventRegistry\ClassReflectionDomainEventRegistry;
use Papyrus\ClassReflectionDomainEventRegistry\DomainEventClassNameLoader;
use Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub\TestDomainEvent;
use Papyrus\DomainEventRegistry\DomainEventNameResolver\DomainEventNameResolver;
use Papyrus\DomainEventRegistry\DomainEventNotRegisteredException;

/**
 * @internal
 */
class ClassReflectionDomainEventRegistryTest extends MockeryTestCase
{
    /**
     * @var MockInterface&DomainEventClassNameLoader<object>
     */
    private MockInterface $domainEventClassNameLoader;

    /**
     * @var MockInterface&DomainEventNameResolver<object>
     */
    private MockInterface $resolver;

    /**
     * @var ClassReflectionDomainEventRegistry<object>
     */
    private ClassReflectionDomainEventRegistry $domainEventRegistry;

    protected function setUp(): void
    {
        $this->domainEventRegistry = new ClassReflectionDomainEventRegistry(
            $this->domainEventClassNameLoader = Mockery::mock(DomainEventClassNameLoader::class),
            $this->resolver = Mockery::mock(DomainEventNameResolver::class),
            'some-dir',
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function itShouldLoadClassNamesWhenNotLoadedYet(): void
    {
        $this->domainEventClassNameLoader->expects('load')->andReturn([
            TestDomainEvent::class,
        ]);

        $this->resolver->expects('resolve')
            ->with(TestDomainEvent::class)
            ->andReturn('test.domain_event')
        ;

        $domainEventClassName = $this->domainEventRegistry->retrieve('test.domain_event');

        self::assertSame(TestDomainEvent::class, $domainEventClassName);
    }

    /**
     * @test
     */
    public function itShouldNotLoadClassNamesAgainWhenAlreadyLoaded(): void
    {
        $this->domainEventClassNameLoader->expects('load')->once()->andReturn([
            TestDomainEvent::class,
        ]);

        $this->resolver->expects('resolve')
            ->with(TestDomainEvent::class)
            ->andReturn('test.domain_event')
        ;

        $domainEventClassName = $this->domainEventRegistry->retrieve('test.domain_event');

        self::assertSame(TestDomainEvent::class, $domainEventClassName);

        $domainEventClassName = $this->domainEventRegistry->retrieve('test.domain_event');

        self::assertSame(TestDomainEvent::class, $domainEventClassName);
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenClassNameIsNotRegistered(): void
    {
        $this->domainEventClassNameLoader->expects('load')->andReturn([
            TestDomainEvent::class,
        ]);

        $this->resolver->expects('resolve')
            ->with(TestDomainEvent::class)
            ->andReturn('test.domain_event')
        ;

        self::expectException(DomainEventNotRegisteredException::class);

        $this->domainEventRegistry->retrieve('event');
    }
}
