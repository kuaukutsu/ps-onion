<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\input;

final readonly class AuthorInput
{
    public function __construct(
        public string $name,
        public ?string $secondName = null,
    ) {
    }
}
