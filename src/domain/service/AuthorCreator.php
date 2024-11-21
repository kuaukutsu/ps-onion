<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorInputDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;

final readonly class AuthorCreator
{
    /**
     * @throws LogicException is input data not valid
     */
    public function createFromInputData(AuthorInputDto $input): Author
    {
        return new Author(
            uuid: AuthorUuidGenerator::generate(),
            person: new AuthorPerson(
                name: $this->prepareName($input->name),
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
        return mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
    }
}
