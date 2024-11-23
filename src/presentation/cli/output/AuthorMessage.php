<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli\output;

use Override;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;

final readonly class AuthorMessage implements OutputMessage
{
    public function __construct(
        private string $uuid,
        private string $name,
    ) {
    }

    public static function fromBook(AuthorDto $author): AuthorMessage
    {
        return new self(
            uuid: $author->uuid,
            name: $author->name,
        );
    }

    #[Override]
    public function output(): array
    {
        return [
            'UUID: ' . $this->uuid,
            'Name: ' . $this->name,
        ];
    }
}
