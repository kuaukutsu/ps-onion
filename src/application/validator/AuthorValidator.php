<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;
use Assert\Assert;

final readonly class AuthorValidator
{
    /**
     * @return array{"name": non-empty-string}
     * @throws LogicException
     */
    public function prepare(array $data): array
    {
        if (array_key_exists('name', $data) === false) {
            throw new LogicException('Name is required.');
        }

        Assert::lazy()
            ->that($data['name'])->string()->notEmpty()
            ->verifyNow();

        /**
         * @var array{"name": non-empty-string}
         */
        return [
            'name' => $data['name'],
        ];
    }
}
