<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;

final readonly class BookImporter
{
    /**
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws LogicException
     */
    public function createFromRawData(string $title, string $author): Book
    {
        return new Book(
            uuid: new BookUuid(),
            title: new BookTitle(name: $title, description: $title),
            author: new BookAuthor(name: $author),
        );
    }
}
