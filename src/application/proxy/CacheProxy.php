<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\proxy;

use Override;
use DateInterval;
use RuntimeException;
use Psr\SimpleCache\CacheInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\infrastructure\cache\FileCache;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class CacheProxy implements CacheInterface
{
    private CacheInterface $cache;

    /**
     * @throws RuntimeException if not create directory
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->cache = new FileCache(
            $application->getRuntime() . DIRECTORY_SEPARATOR . 'cache'
        );
    }

    #[Override]
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    #[Override]
    public function set(string $key, mixed $value, DateInterval | int | null $ttl = null): bool
    {
        return $this->cache->set($key, $value, $ttl);
    }

    #[Override]
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }

    #[Override]
    public function clear(): bool
    {
        return $this->cache->clear();
    }

    #[Override]
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function getMultiple(iterable $keys, mixed $default = null): never
    {
        throw new NotImplementedException();
    }

    /**
     * @param iterable<mixed, mixed> $values
     * @throws NotImplementedException
     */
    #[Override]
    public function setMultiple(iterable $values, DateInterval | int | null $ttl = null): never
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function deleteMultiple(iterable $keys): never
    {
        throw new NotImplementedException();
    }
}
