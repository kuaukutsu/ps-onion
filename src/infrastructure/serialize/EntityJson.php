<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\serialize;

use Throwable;
use InvalidArgumentException;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class EntityJson
{
    private function __construct()
    {
    }

    /**
     * @param array<string, scalar|array|null> $entityData
     * @throws InvalidArgumentException
     */
    public static function encode(array $entityData): string
    {
        try {
            return json_encode($entityData, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
