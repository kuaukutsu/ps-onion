<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class AuthorCreator
{
    /**
     * @param non-empty-string $name
     * @throws LogicException
     */
    public function createFromRawData(string $name): Author
    {
        return new Author(
            uuid: new AuthorUuid(),
            person: new AuthorPerson(
                name: $name,
            ),
            metadata: new AuthorMetadata(),
        );
    }
}
