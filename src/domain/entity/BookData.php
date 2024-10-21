<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class BookData implements EntityDto
{
    public function __construct(
        private string $uuid,
        private string $title,
        private string $author,
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
