<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

use kuaukutsu\ps\onion\domain\interface\Entity;

final readonly class Author implements Entity
{
    public function __construct(
        public AuthorUuid $uuid,
        public AuthorPerson $person,
        public AuthorMetadata $metadata,
    ) {
    }

    /**
     * @param non-empty-string $name
     */
    public function changeName(string $name): self
    {
        return new self(
            $this->uuid,
            new AuthorPerson(
                name: $name,
                surname: $this->person->surname,
            ),
            new AuthorMetadata(
                createdAt: $this->metadata->createdAt,
            ),
        );
    }
}
