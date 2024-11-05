<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

use LogicException;

final readonly class AuthorMapper
{
    private function __construct()
    {
    }

    /**
     * @throws LogicException is input data not valid
     */
    public static function toModel(AuthorDto $dto): Author
    {
        return new Author(
            uuid: new AuthorUuid($dto->uuid),
            person: new AuthorPerson($dto->name),
            metadata: new AuthorMetadata($dto->createdAt, $dto->updatedAt)
        );
    }

    public static function toDto(Author $author): AuthorDto
    {
        return new AuthorDto(
            uuid: $author->uuid->value,
            name: $author->person->name,
            createdAt: $author->metadata->createdAt,
            updatedAt: $author->metadata->updatedAt,
        );
    }
}
