<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use LogicException;

final readonly class BookMapper
{
    private function __construct()
    {
    }

    /**
     * @throws LogicException is input data not valid
     */
    public static function toModel(BookDto $dto): Book
    {
        return new Book(
            uuid: new BookUuid($dto->uuid),
            title: new BookTitle($dto->title, $dto->description),
            author: new BookAuthor($dto->author),
        );
    }

    public static function toDto(Book $book): BookDto
    {
        return new BookDto(
            uuid: $book->uuid->value,
            title: $book->title->name,
            description: $book->title->description,
            author: $book->author->name,
        );
    }
}
