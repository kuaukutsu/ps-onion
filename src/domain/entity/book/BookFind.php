<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use kuaukutsu\ps\onion\domain\interface\Entity;

final readonly class BookFind implements Entity
{
    public function __construct(
        public BookTitle $title,
        public ?BookAuthor $author = null,
    ) {
    }
}
