<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\book;

use TypeError;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\case\author\View as AuthorView;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\application\validator\BookImportValidator;
use kuaukutsu\ps\onion\application\validator\IsbnValidator;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\BookRepository;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class View
{
    public function __construct(
        private AuthorView $author,
        private BookImportValidator $validator,
        private BookRepository $repository,
        private IsbnValidator $isbnValidator,
        private BookMapper $mapper,
    ) {
    }

    /**
     * @param non-empty-string $isbn
     * @throws TypeError is output data error
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws InvalidArgumentException validation data
     */
    public function getByISBN(string $isbn): BookDto
    {
        $this->isbnValidator->exception($isbn);
        return $this->mapper->toDto(
            $this->repository->get(
                new BookIsbn($isbn)
            )
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     */
    public function getByName(BookInput $input): BookDto
    {
        $bookTitle = $this->validator->prepareTitle($input);
        $bookAuthor = $this->validator->prepareAuthor($input);

        $book = $this->authorExists($bookAuthor)
            ? $this->repository->find($bookTitle, $bookAuthor)
            : $this->repository->find($bookTitle);

        if ($book instanceof Book) {
            return $this->mapper->toDto($book);
        }

        throw new NotFoundException("Book '$bookTitle->name' not found.");
    }

    /**
     * @throws LogicException
     * @throws InfrastructureException
     */
    private function authorExists(?BookAuthor $bookAuthor): bool
    {
        if ($bookAuthor === null) {
            return false;
        }

        return $this->author->exists(
            new AuthorInput(
                name: $bookAuthor->name
            )
        );
    }
}
