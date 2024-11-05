<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

final readonly class AuthorPerson
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public string $name,
    ) {
    }
}
