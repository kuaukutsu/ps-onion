<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\input;

final readonly class BookInput
{
    public function __construct(
        public string $title,
        public ?AuthorInput $author = null,
    ) {
    }
}
