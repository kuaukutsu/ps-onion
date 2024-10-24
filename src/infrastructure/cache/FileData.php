<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\cache;

use DateInterval;
use DateMalformedIntervalStringException;
use DateTimeImmutable;

final readonly class FileData
{
    private function __construct()
    {
    }

    public static function serialize(mixed $value, DateInterval | int | null $ttl = null): string
    {
        $convertToTimestamp = static function (DateInterval $dateInterval): int {
            return (new DateTimeImmutable())
                ->add($dateInterval)
                ->getTimestamp();
        };

        try {
            $second = match (true) {
                is_int($ttl) => $convertToTimestamp(new DateInterval('PT' . $ttl . 'S')),
                $ttl instanceof DateInterval => $convertToTimestamp($ttl),
                default => 0,
            };
        } catch (DateMalformedIntervalStringException) {
            $second = 0;
        }

        return serialize(
            [
                'ttl' => $second,
                'payload' => $value,
            ]
        );
    }

    public static function unserialize(string $data): mixed
    {
        $container = unserialize($data, ['allowed_classes' => true]);
        if (is_array($container) === false || isset($container['payload']) === false) {
            return false;
        }

        if (
            isset($container['ttl'])
            && $container['ttl'] > 0
            && $container['ttl'] < time()
        ) {
            return false;
        }

        return $container['payload'];
    }

    public static function checkTtl(string $data): bool
    {
        $container = unserialize($data, ['allowed_classes' => true]);
        return (is_array($container)
            && isset($container['ttl'])
            && $container['ttl'] > 0
            && $container['ttl'] < time()) === false;
    }
}
