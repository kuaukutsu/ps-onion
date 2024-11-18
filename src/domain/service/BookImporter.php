<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookInputDto;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;

final readonly class BookImporter
{
    /**
     * @throws LogicException
     */
    public function createFromInputData(BookInputDto $input, Author $author): Book
    {
        return new Book(
            uuid: new BookUuid(),
            title: new BookTitle(name: $input->title, description: $input->description),
            author: new BookAuthor(name: $author->person->name),
        );
    }
}
