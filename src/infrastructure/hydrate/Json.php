<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\hydrate;

use InvalidArgumentException;
use JsonException;

final readonly class Json
{
    /**
     * @throws InvalidArgumentException
     */
    public static function encode(array $entityData): string
    {
        try {
            return json_encode($entityData, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
