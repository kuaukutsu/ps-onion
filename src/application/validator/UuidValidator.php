<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use InvalidArgumentException;
use Ramsey\Uuid\Validator\ValidatorInterface;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class UuidValidator
{
    public function __construct(
        private ValidatorInterface $uuidValidator,
    ) {
    }

    /**
     * @param string $uuid
     * @throws InvalidArgumentException
     */
    public function exception(string $uuid): void
    {
        if ($this->validate($uuid) === false) {
            throw new InvalidArgumentException("UUID '$uuid' is not valid.");
        }
    }

    /**
     * @param string $uuid The string to validate as a UUID
     * @return bool True if the string is a valid UUID, false otherwise
     */
    public function validate(string $uuid): bool
    {
        return $this->uuidValidator->validate($uuid);
    }
}
