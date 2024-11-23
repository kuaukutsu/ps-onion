<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\BookImportValidator;
use kuaukutsu\ps\onion\application\validator\IsbnValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorInputDto;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookMapper;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;
use kuaukutsu\ps\onion\domain\service\BookImporter;

/**
 * @api
 */
final readonly class Bookshelf
{
    public function __construct(
        private AuthorCreator $authorCreator,
        private AuthorRepository $authorRepository,
        private BookImporter $importer,
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
    public function find(array $data): BookDto
    {
        $prepareData = $this->bookImportValidator->prepare($data);
        $author = $this->makeAuthor(
            new AuthorInputDto(name: $prepareData['author'])
        );

        $book = $this->bookRepository->find(
            new BookTitle(name: $prepareData['title']),
            new BookAuthor(name: $author->person->name)
        );

        if ($book instanceof Book) {
            return BookMapper::toDto($book);
        }

        throw new NotFoundException("Book '{$prepareData['title']}' not found.");
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function import(array $data): BookDto
    {
        $prepareData = $this->bookImportValidator->prepare($data);
        $author = $this->makeAuthor(
            new AuthorInputDto(name: $prepareData['author'])
        );

        $book = $this->importer->createFromInputData(
            new BookTitle(name: $prepareData['title']),
            new BookAuthor(name: $author->person->name),
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
    private function makeAuthor(AuthorInputDto $inputDto): Author
    {
        $author = $this->authorCreator->createFromInputData($inputDto);
        $listAuthor = $this->authorRepository->find($author->person);
        if ($listAuthor === []) {
            return $this->authorRepository->save($author);
        }

        // @note: Логика выбора нужного автора из списка если поиск вернул больше одного.
        return current($listAuthor);
    }
}
