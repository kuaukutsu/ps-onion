<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class AuthorValidator
{
    public function __construct(private AuthorNameValidator $authorNameValidator)
    {
    }

    /**
     * @throws LogicException
     */
    public function prepare(AuthorInput $data): AuthorPerson
    {
        /** @var non-empty-string $name */
        $name = trim($data->name);
        $this->authorNameValidator->validate($name);

        $secondName = null;
        if ($data->secondName !== null) {
            /** @var non-empty-string $secondName */
            $secondName = trim($data->secondName);
            $this->authorNameValidator->validate($secondName);
        }

        return new AuthorPerson(
            name: $name,
            secondName: $secondName,
        );
    }
}
