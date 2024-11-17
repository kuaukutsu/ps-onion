<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;

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
                name: $this->prepareName($name),
            ),
            metadata: new AuthorMetadata(),
        );
    }

    /**
     * Domain logic
     * @param non-empty-string $name
     * @return non-empty-string
     */
    private function prepareName(string $name): string
    {
        /**
         * @var non-empty-string
         */
        return mb_ucfirst(mb_strtolower($name));
    }
}
