<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use Ramsey\Uuid\UuidFactoryInterface;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;

final readonly class AuthorUuidGenerator
{
    public function __construct(private UuidFactoryInterface $uuidFactory)
    {
    }

    public function generate(): AuthorUuid
    {
        return new AuthorUuid(
            $this->uuidFactory->uuid4()->toString(),
        );
    }
}
