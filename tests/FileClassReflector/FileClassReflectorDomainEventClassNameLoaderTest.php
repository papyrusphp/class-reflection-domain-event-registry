<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector;

use Jerowork\FileClassReflector\ClassReflectorFactory;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Papyrus\ClassReflectionDomainEventRegistry\FileClassReflector\FileClassReflectorDomainEventClassNameLoader;
use Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub\TestDomainEvent;
use Papyrus\ClassReflectionDomainEventRegistry\Test\FileClassReflector\Stub\TestAnotherDomainEvent;
use ReflectionClass;

/**
 * @internal
 */
class FileClassReflectorDomainEventClassNameLoaderTest extends MockeryTestCase
{
    /**
     * @var MockInterface&ClassReflectorFactory
     */
    private MockInterface $classReflectorFactory;

    /**
     * @var FileClassReflectorDomainEventClassNameLoader<object>
     */
    private FileClassReflectorDomainEventClassNameLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new FileClassReflectorDomainEventClassNameLoader(
            $this->classReflectorFactory = Mockery::mock(ClassReflectorFactory::class),
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function itShouldLoadClassNamesFromDirectory(): void
    {
        $this->classReflectorFactory
            ->expects('create->addDirectory->reflect->getClasses')
            ->andReturn([
                new ReflectionClass(TestDomainEvent::class),
                new ReflectionClass(TestAnotherDomainEvent::class),
            ])
        ;

        $classNames = $this->loader->load('some-dir');

        self::assertSame([
            TestDomainEvent::class,
            TestAnotherDomainEvent::class,
        ], $classNames);
    }
}
