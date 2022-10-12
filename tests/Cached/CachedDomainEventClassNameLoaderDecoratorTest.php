<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Test\Cached;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Papyrus\ClassReflectionDomainEventRegistry\Cached\CachedDomainEventClassNameLoaderDecorator;
use Papyrus\ClassReflectionDomainEventRegistry\DomainEventClassNameLoader;
use Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub\TestDomainEvent;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 */
class CachedDomainEventClassNameLoaderDecoratorTest extends MockeryTestCase
{
    /**
     * @var MockInterface&DomainEventClassNameLoader
     */
    private MockInterface $domainEventClassNameLoader;

    /**
     * @var MockInterface&CacheInterface
     */
    private MockInterface $cache;

    private CachedDomainEventClassNameLoaderDecorator $decorator;

    protected function setUp(): void
    {
        $this->decorator = new CachedDomainEventClassNameLoaderDecorator(
            $this->domainEventClassNameLoader = Mockery::mock(DomainEventClassNameLoader::class),
            $this->cache = Mockery::mock(CacheInterface::class),
            'a-key',
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function itShouldGetFromDecoratedLoaderAndStoreInCache(): void
    {
        $this->cache->expects('has')->andReturn(false);

        $this->domainEventClassNameLoader->expects('load')
            ->with('some-dir')
            ->andReturn([
                TestDomainEvent::class,
            ])
        ;

        $this->cache->expects('set')->with(
            'a-key',
            '["Papyrus\\\\ClassReflectionDomainEventRegistry\\\\Test\\\\FileClassReflector\\\\Stub\\\\TestDomainEvent"]',
        );

        $classNames = $this->decorator->load('some-dir');

        self::assertSame([
            TestDomainEvent::class,
        ], $classNames);
    }

    /**
     * @test
     */
    public function itShouldGetFromCache(): void
    {
        $this->cache->expects('has')->andReturn(true);

        $this->cache->expects('get')
            ->with('a-key')
            ->andReturn('["Papyrus\\\\ClassReflectionDomainEventRegistry\\\\Test\\\\FileClassReflector\\\\Stub\\\\TestDomainEvent"]')
        ;

        $this->domainEventClassNameLoader->expects('load')->never();

        $this->cache->expects('set')->never();

        $classNames = $this->decorator->load('some-dir');

        self::assertSame([
            TestDomainEvent::class,
        ], $classNames);
    }
}
