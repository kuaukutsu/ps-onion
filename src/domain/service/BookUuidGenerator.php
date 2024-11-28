<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use Ramsey\Uuid\UuidFactoryInterface;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;

final readonly class BookUuidGenerator
{
    public function __construct(private UuidFactoryInterface $uuidFactory)
    {
    }

    public function generate(): BookUuid
    {
        return new BookUuid(
            $this->uuidFactory->uuid4()->toString(),
        );
    }

    public function generateByKey(string $key): BookUuid
    {
        return new BookUuid(
            $this->uuidFactory
                ->fromString(
                    hash('xxh128', $key)
                )
                ->toString(),
        );
    }
}
