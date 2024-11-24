<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\cache;

use Override;
use DateInterval;
use Psr\SimpleCache\CacheInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;

/**
 * Минимально необходимая реализация для CacheInterface
 */
final readonly class NullCache implements CacheInterface
{
    #[Override]
    public function get(string $key, mixed $default = null): false
    {
        return false;
    }

    #[Override]
    public function set(string $key, mixed $value, DateInterval | int | null $ttl = null): bool
    {
        return true;
    }

    #[Override]
    public function delete(string $key): bool
    {
        return true;
    }

    #[Override]
    public function clear(): bool
    {
        return true;
    }

    #[Override]
    public function has(string $key): bool
    {
        return false;
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
