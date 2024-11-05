<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 */
final readonly class Cache
{
    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Generated string key
     */
    public static function makeKey(string ...$keys): string
    {
        return 'book:' . implode('-', $keys);
    }

    public function get(string $key): ?BookDto
    {
        try {
            $model = $this->cache->get($key);
        } catch (InvalidArgumentException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception, ['key' => $key]),
                __METHOD__,
            );

            return null;
        }

        if ($model instanceof BookDto) {
            return $model;
        }

        return null;
    }

    public function set(string $key, BookDto $book): void
    {
        try {
            if ($this->cache->has($key) === false) {
                $this->cache->set($key, $book);
            }
        } catch (InvalidArgumentException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception, ['key' => $key]),
                __METHOD__,
            );
        }
    }
}
