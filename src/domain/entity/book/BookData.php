<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class BookData implements EntityDto
{
    public function __construct(
        private string $uuid,
        private string $title,
        private string $author,
    ) {
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'author' => $this->author,
        ];
    }
}
