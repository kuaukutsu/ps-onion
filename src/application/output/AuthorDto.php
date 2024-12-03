<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\output;

use kuaukutsu\ps\onion\domain\entity\author\Author;

final readonly class AuthorDto
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

    public static function fromEntity(Author $author): self
    {
        return new AuthorDto(
            uuid: $author->uuid->value,
            name: $author->person->name,
            createdAt: $author->metadata->createdAt,
            updatedAt: $author->metadata->updatedAt,
        );
    }
}
