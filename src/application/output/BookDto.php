<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\output;

use kuaukutsu\ps\onion\domain\entity\book\Book;

final readonly class BookDto
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

    public static function fromEntity(Book $book): BookDto
    {
        return new BookDto(
            uuid: $book->uuid->value,
            title: $book->title->name,
            author: $book->author->name,
            description: $book->title->description,
        );
    }
}
