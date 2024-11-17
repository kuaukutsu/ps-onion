<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;
use Assert\Assert;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class BookValidator
{
    /**
     * @return array{"title": non-empty-string, "author": non-empty-string}
     * @throws LogicException
     */
    public function prepare(array $data): array
    {
        if (array_key_exists('title', $data) === false) {
            throw new LogicException('Title is required.');
        }

        if (array_key_exists('author', $data) === false) {
            throw new LogicException('Author is required.');
        }

        Assert::lazy()
            ->that($data['title'])->string()->notEmpty()
            ->that($data['author'])->string()->notEmpty()
            ->verifyNow();

        /**
         * @var array{"title": non-empty-string, "author": non-empty-string}
         */
        return [
            'title' => $data['title'],
            'author' => $data['author'],
        ];
    }
}
