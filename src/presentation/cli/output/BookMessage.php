<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli\output;

use Override;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;

final readonly class BookMessage implements ConsoleMessage
{
    public function __construct(
        private string $uuid,
        private string $title,
        private string $authorName,
    ) {
    }

    public static function fromBook(BookDto $book): BookMessage
    {
        return new self(
            uuid: $book->uuid,
            title: $book->title,
            authorName: $book->author,
        );
    }

    #[Override]
    public function output(): array
    {
        return [
            'UUID: ' . $this->uuid,
            'Title: ' . $this->title,
            'Author: ' . $this->authorName,
        ];
    }
}
