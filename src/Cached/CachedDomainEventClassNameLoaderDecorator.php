<?php

declare(strict_types=1);

namespace Papyrus\ClassReflectionDomainEventRegistry\Cached;

use JsonException;
use Papyrus\ClassReflectionDomainEventRegistry\DomainEventClassNameLoader;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class CachedDomainEventClassNameLoaderDecorator implements DomainEventClassNameLoader
{
    private const CACHE_KEY = 'papyrus.class-reflection-domain-event-registry';

    public function __construct(
        private readonly DomainEventClassNameLoader $domainEventClassNameLoader,
        private readonly CacheInterface $cache,
        private readonly string $cacheKey = self::CACHE_KEY,
    ) {
    }

    /**
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public function load(string $directory): array
    {
        if ($this->cache->has($this->cacheKey) === false) {
            $classNames = $this->domainEventClassNameLoader->load($directory);

            $this->cache->set($this->cacheKey, json_encode($classNames, JSON_THROW_ON_ERROR));

            return $classNames;
        }

        /** @var string $cachedResult */
        $cachedResult = $this->cache->get($this->cacheKey);

        /* @phpstan-ignore-next-line */
        return json_decode($cachedResult, true, flags: JSON_THROW_ON_ERROR);
    }
}
