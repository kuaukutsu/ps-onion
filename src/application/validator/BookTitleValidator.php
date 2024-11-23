<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;
use Assert\Assert;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class BookTitleValidator
{
    /**
     * @throws LogicException
     */
    public function validate(mixed $value): void
    {
        Assert::that($value)
            ->string()
            ->maxLength(256)
            ->notEmpty();
    }
}
