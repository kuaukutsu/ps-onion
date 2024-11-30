<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\output;

use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;

final readonly class BookMapper
{
    public function toDto(Book $book): BookDto
    {
        return new BookDto(
            uuid: $book->uuid->value,
            title: $book->title->name,
            author: $book->author->name,
            description: $book->title->description,
        );
    }
}
