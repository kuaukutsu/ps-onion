<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

final readonly class BookTitle
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|null $description
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {
    }
}
