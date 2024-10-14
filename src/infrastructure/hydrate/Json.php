<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\hydrate;

use Throwable;
use InvalidArgumentException;

final readonly class Json
{
    /**
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
