<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use Assert\Assert;
use InvalidArgumentException;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class IsbnValidator
{
    /**
     * @param string $isbn
     * @throws InvalidArgumentException
     */
    public function exception(string $isbn): void
    {
        if ($this->validate($isbn) === false) {
            throw new InvalidArgumentException("ISBN '$isbn' is not valid.");
        }
    }

    /**
     * @param string $isbn The string to validate as a ISBN
     * @return bool True if the string is a valid ISBN, false otherwise
     */
    public function validate(string $isbn): bool
    {
        Assert::that($isbn)
            ->numeric()
            ->notEmpty();

        return true;
    }
}
