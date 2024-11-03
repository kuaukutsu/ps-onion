<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\Entity;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class Author implements Entity
{
    public function __construct(
        public string $uuid,
        public string $name,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
