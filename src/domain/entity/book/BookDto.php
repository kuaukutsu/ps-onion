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
     * @param non-empty-string $author
     * @param non-empty-string|null $description
     */
    public function __construct(
        public string $uuid,
        public string $title,
        public string $author,
        public ?string $description = null,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
        ];
    }
}
