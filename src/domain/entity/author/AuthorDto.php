<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class AuthorDto implements EntityDto
{
    /**
     * @param non-empty-string $uuid
     * @param non-empty-string $name
     * @param non-empty-string $createdAt
     * @param non-empty-string $updatedAt
     */
    public function __construct(
        public string $uuid,
        public string $name,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [];
    }
}
