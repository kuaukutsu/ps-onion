<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;
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

    public function get(string $key): ?EntityDto
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

        if ($model instanceof EntityDto) {
            return $model;
        }

        return null;
    }

    public function set(string $key, EntityDto $book): void
    {
        try {
            $this->cache->set($key, $book);
        } catch (InvalidArgumentException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception, ['key' => $key]),
                __METHOD__,
            );
        }
    }
}
