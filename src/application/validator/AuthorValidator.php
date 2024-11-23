<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class AuthorValidator
{
    public function __construct(private AuthorNameValidator $authorNameValidator)
    {
    }

    /**
     * @return array{"name": non-empty-string}
     * @throws LogicException
     */
    public function prepare(array $data): array
    {
        if (array_key_exists('name', $data) === false) {
            throw new LogicException('Name is required.');
        }

        $this->authorNameValidator->validate($data['name']);

        /**
         * @var array{"name": non-empty-string}
         */
        return [
            'name' => $data['name'],
        ];
    }
}
