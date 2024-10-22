<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\book;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use kuaukutsu\ps\onion\domain\entity\Book;

final readonly class BookCache
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function find(string $uuid): ?Book
    {
        try {
            $model = $this->cache->get($this->generateKey($uuid));
        } catch (InvalidArgumentException) {
            return null;
        }

        if ($model instanceof Book) {
            return $model;
        }

        return null;
    }

    public function set(Book $book): void
    {
        $key = $this->generateKey($book->uuid);

        try {
            if ($this->cache->has($key) === false) {
                $this->cache->set($key, $book);
            }
        } catch (InvalidArgumentException) {
        }
    }

    private function generateKey(string $uuid): string
    {
        return 'book:' . $uuid;
    }
}
