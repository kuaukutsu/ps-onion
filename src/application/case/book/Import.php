<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\book;

use LogicException;
use kuaukutsu\ps\onion\application\case\author\Create;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\application\output\BookMapper;
use kuaukutsu\ps\onion\application\validator\BookImportValidator;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
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
        private BookMapper $mapper,
    ) {
    }

    /**
     * @throws LogicException is input data not valid
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

        return $this->mapper->toDto($book);
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    private function saveBook(BookTitle $bookTitle, BookAuthor $bookAuthor): Book
    {
        $book = $this->creator->createFromInputData(
            $bookTitle,
            $bookAuthor,
        );

        return $this->repository->find($book->title, $book->author)
            ?? $this->repository->import($book);
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
