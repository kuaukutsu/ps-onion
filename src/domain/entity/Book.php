<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\Entity;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class Book implements Entity
{
    public function __construct(
        public string $uuid,
        public string $title,
        public string $author,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'author' => $this->author,
        ];
    }
}
