<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;

final readonly class BookCreator
{
    public function __construct(private BookUuidGenerator $uuidGenerator)
    {
    }

    /**
     * @throws LogicException
     */
    public function createFromInputData(BookTitle $title, BookAuthor $author): Book
    {
        return new Book(
            uuid: $this->uuidGenerator->generate(),
            title: $title,
            author: $author,
        );
    }
}
