<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\book;

use LogicException;
use kuaukutsu\ps\onion\application\case\author\Create;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\application\validator\BookImportValidator;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookMapper;
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
        private BookCreator $bookCreator,
        private BookRepository $bookRepository,
        private BookImportValidator $bookImportValidator,
    ) {
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function push(BookInput $input): BookDto
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
        $author = $this->authorCreate->createIfNotExists(
            new AuthorInput(
                name: $bookAuthor->name
            )
        );

        return new BookAuthor(
            name: $author->name
        );
    }
}
