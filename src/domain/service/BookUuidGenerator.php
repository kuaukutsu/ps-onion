<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use Ramsey\Uuid\Uuid;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;

final readonly class BookUuidGenerator
{
    private function __construct()
    {
    }

    public static function generate(): BookUuid
    {
        return new BookUuid(
            Uuid::uuid4()->toString()
        );
    }

    public static function generateByIsbn(string $isbn): BookUuid
    {
        return new BookUuid(
            Uuid::uuid8($isbn)->toString()
        );
    }
}
