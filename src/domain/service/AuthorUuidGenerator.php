<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use Ramsey\Uuid\Uuid;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;

final readonly class AuthorUuidGenerator
{
    private function __construct()
    {
    }

    public static function generate(): AuthorUuid
    {
        return new AuthorUuid(
            Uuid::uuid4()->toString()
        );
    }
}
