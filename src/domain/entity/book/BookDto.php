<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class BookDto implements EntityDto
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
