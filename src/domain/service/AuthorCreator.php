<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;

final readonly class AuthorCreator
{
    public function __construct(private AuthorUuidGenerator $uuidGenerator)
    {
    }

    public function createFromInputData(AuthorPerson $person): Author
    {
        return new Author(
            uuid: $this->uuidGenerator->generate(),
            person: $this->preparePerson($person),
            metadata: new AuthorMetadata(),
        );
    }

    /**
     * Domain logic
     */
    private function preparePerson(AuthorPerson $person): AuthorPerson
    {
        /**
         * @var non-empty-string $name
         */
        $name = mb_convert_case($person->name, MB_CASE_TITLE, "UTF-8");

        $secondName = null;
        if (is_string($person->secondName)) {
            /** @var non-empty-string $secondName */
            $secondName = mb_convert_case($person->secondName, MB_CASE_TITLE, "UTF-8");
        }

        return new AuthorPerson(
            name: $name,
            secondName: $secondName,
        );
    }
}
