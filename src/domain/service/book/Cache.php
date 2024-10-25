<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\book;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use kuaukutsu\ps\onion\domain\entity\Book;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain\service\book
 */
final readonly class Cache
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    /**
     * Generated string key
     */
    public static function makeKey(string ...$keys): string
    {
        return 'book:' . implode('-', $keys);
    }

    public function get(string $key): ?Book
    {
        try {
            $model = $this->cache->get($key);
        } catch (InvalidArgumentException) {
            return null;
        }

        if ($model instanceof Book) {
            return $model;
        }

        return null;
    }

    public function set(string $key, Book $book): void
    {
        try {
            if ($this->cache->has($key) === false) {
                $this->cache->set($key, $book);
            }
        } catch (InvalidArgumentException) {
        }
    }
}
