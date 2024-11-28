<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;

final readonly class Mapper
{
    public function fromDto(AuthorDto $dto): Author
    {
        return new Author(
            uuid: new AuthorUuid($dto->uuid),
            person: new AuthorPerson($dto->name),
            metadata: new AuthorMetadata($dto->createdAt, $dto->updatedAt)
        );
    }

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
