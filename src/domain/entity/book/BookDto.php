<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class BookDto implements EntityDto
{
    /**
     * @param non-empty-string $uuid
     * @param non-empty-string $title
     * @param non-empty-string $description
     * @param non-empty-string $author
     */
    public function __construct(
        public string $uuid,
        public string $title,
        public string $description,
        public string $author,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
        ];
    }
}
