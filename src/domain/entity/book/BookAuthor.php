<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

final readonly class BookAuthor
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public string $name,
    ) {
    }
}
