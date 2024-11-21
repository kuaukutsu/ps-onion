<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class OpenlibrarySchema implements EntityDto
{
    /**
     * @param array<array{
     *     "title": non-empty-string,
     *     "firstPublishYear": int,
     *     "authorName": non-empty-string[],
     *     "isbn": non-empty-string[],
     * }> $docs
     */
    public function __construct(
        public int $numFound,
        public array $docs,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'numFound' => $this->numFound,
            'docs' => $this->docs,
        ];
    }
}
