<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\output;

use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;

final readonly class AuthorMapper
{
    public function toDto(Author $author): AuthorDto
    {
        return new AuthorDto(
            uuid: $author->uuid->value,
            name: $author->person->name,
            createdAt: $author->metadata->createdAt,
            updatedAt: $author->metadata->updatedAt,
        );
    }
}
