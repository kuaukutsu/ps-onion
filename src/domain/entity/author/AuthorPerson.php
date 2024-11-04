<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

final readonly class AuthorPerson
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $surname
     */
    public function __construct(
        public string $name,
        public string $surname,
    ) {
    }
}
