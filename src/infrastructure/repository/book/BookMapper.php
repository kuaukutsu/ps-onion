<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use TypeError;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;
use kuaukutsu\ps\onion\domain\service\BookUuidGenerator;

final readonly class BookMapper
{
    public function __construct(private BookUuidGenerator $uuidGenerator)
    {
    }

    /**
     * @throws TypeError если получены не корректные данные
     */
    public function fromOpenlibraryBook(OpenlibraryBook $openlibraryBook): Book
    {
        return new Book(
            uuid: $this->uuidGenerator->generateByKey($openlibraryBook->key),
            title: new BookTitle(name: $openlibraryBook->title),
            author: new BookAuthor(
                name: $this->prepareOpenlibraryBookAuthor($openlibraryBook)
            ),
        );
    }

    public function fromDto(BookDto $dto): Book
    {
        return new Book(
            uuid: new BookUuid($dto->uuid),
            title: new BookTitle(name: $dto->title),
            author: new BookAuthor(name: $dto->author),
        );
    }

    public function toDto(Book $book): BookDto
    {
        return new BookDto(
            uuid: $book->uuid->value,
            title: $book->title->name,
            author: $book->author->name,
            description: $book->title->description,
        );
    }

    /**
     * @return non-empty-string
     * @throws TypeError
     */
    private function prepareOpenlibraryBookAuthor(OpenlibraryBook $openlibraryBook): string
    {
        $name = current($openlibraryBook->authorName);
        if (is_string($name)) {
            return $name;
        }

        throw new TypeError("AuthorName must be string.");
    }
}
