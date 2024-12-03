<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\book;

use TypeError;
use LogicException;
use kuaukutsu\ps\onion\application\case\author\Create;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\application\output\BookDto;
use kuaukutsu\ps\onion\application\validator\BookImportValidator;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\BookCreator;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class Import
{
    public function __construct(
        private Create $authorCreate,
        private BookCreator $creator,
        private BookRepository $repository,
        private BookImportValidator $importValidator,
    ) {
    }

    /**
     * @throws LogicException is input data not valid
     * @throws TypeError is inner data not valid
     * @throws InfrastructureException
     */
    public function push(BookInput $input): BookDto
    {
        $bookTitle = $this->importValidator->prepareTitle($input);
        $bookAuthor = $this->importValidator->prepareAuthor($input);
        if ($bookAuthor === null) {
            throw new LogicException("Author is required.");
        }

        $book = $this->saveBook($bookTitle, $bookAuthor);
        $this->saveAuthor($book->author);

        return BookDto::fromEntity($book);
    }

    /**
     * @throws LogicException is input data not valid
     * @throws TypeError is inner data not valid
     * @throws InfrastructureException
     */
    private function saveBook(BookTitle $bookTitle, BookAuthor $bookAuthor): Book
    {
        $book = $this->repository->find($bookTitle, $bookAuthor);
        if ($book === null) {
            $book = $this->creator->createFromInputData(
                $bookTitle,
                $bookAuthor,
            );

            $this->repository->import($book);
        }

        return $book;
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    private function saveAuthor(BookAuthor $bookAuthor): void
    {
        $this->authorCreate->createIfNotExists(
            new AuthorInput(
                name: $bookAuthor->name
            )
        );
    }
}
