<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

final readonly class AuthorPerson
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|null $secondName
     */
    public function __construct(
        public string $name,
        public ?string $secondName = null,
    ) {
    }
}
