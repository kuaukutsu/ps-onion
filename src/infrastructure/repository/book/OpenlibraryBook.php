<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class OpenlibraryBook implements EntityDto
{
    /**
     * @param non-empty-string $key
     * @param non-empty-string $title
     * @param non-empty-string[] $authorName
     * @param non-empty-string[] $isbn
     */
    public function __construct(
        public string $key,
        public string $title,
        public int $firstPublishYear,
        public array $authorName,
        public array $isbn,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'title' => $this->title,
            'firstPublishYear' => $this->firstPublishYear,
            'authorName' => $this->authorName,
            'isbn' => $this->isbn,
        ];
    }
}
