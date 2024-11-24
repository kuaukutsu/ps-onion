<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\BookImportValidator;
use kuaukutsu\ps\onion\application\validator\IsbnValidator;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookMapper;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;
use kuaukutsu\ps\onion\domain\service\AuthorSearch;
use kuaukutsu\ps\onion\domain\service\BookCreator;

/**
 * @api
 */
final readonly class Bookshelf
{
    public function __construct(
        private AuthorCreator $authorCreator,
        private AuthorSearch $authorSearch,
        private AuthorRepository $authorRepository,
        private BookCreator $bookCreator,
        private BookRepository $bookRepository,
        private BookImportValidator $bookImportValidator,
        private IsbnValidator $isbnValidator,
    ) {
    }

    /**
     * @param non-empty-string $isbn
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws InvalidArgumentException validation data
     */
    public function get(string $isbn): BookDto
    {
        $this->isbnValidator->exception($isbn);
        return BookMapper::toDto(
            $this->bookRepository->get(
                new BookIsbn($isbn)
            )
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     */
    public function find(BookInput $input): BookDto
    {
        $bookTitle = $this->bookImportValidator->prepareTitle($input);
        $bookAuthor = $this->bookImportValidator->prepareAuthor($input);

        $book = $this->findAuthor($bookAuthor) instanceof Author
            ? $this->bookRepository->find($bookTitle, $bookAuthor)
            : $this->bookRepository->find($bookTitle);

        if ($book instanceof Book) {
            return BookMapper::toDto($book);
        }

        throw new NotFoundException("Book '$bookTitle->name' not found.");
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function import(BookInput $input): BookDto
    {
        $bookTitle = $this->bookImportValidator->prepareTitle($input);
        $bookAuthor = $this->bookImportValidator->prepareAuthor($input);
        if ($bookAuthor === null) {
            throw new LogicException("Author is required.");
        }

        $book = $this->bookCreator->createFromInputData(
            $bookTitle,
            $this->makeAuthor($bookAuthor),
        );

        return BookMapper::toDto(
            $this->bookRepository->find($book->title, $book->author)
            ?? $this->bookRepository->import($book)
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    private function makeAuthor(BookAuthor $bookAuthor): BookAuthor
    {
        $author = $this->findAuthor($bookAuthor)
            ?? $this->authorRepository->save(
                $this->authorCreator->createFromInputData(
                    new AuthorPerson(
                        name: $bookAuthor->name
                    )
                )
            );

        return new BookAuthor(name: $author->person->name);
    }

    /**
     * @throws InfrastructureException
     */
    private function findAuthor(?BookAuthor $bookAuthor): ?Author
    {
        if ($bookAuthor === null) {
            return null;
        }

        return $this->authorSearch->find(
            $this->authorRepository->find(
                new AuthorPerson(
                    name: $bookAuthor->name
                )
            ),
        );
    }
}
