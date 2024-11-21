<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\BookValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorInputDto;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookInputDto;
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
        private BookValidator $bookValidator,
    ) {
    }

    /**
     * @param non-empty-string $isbn
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws InvalidArgumentException validation data
     */
    public function get(string $isbn): Book
    {
        return $this->bookRepository->get(
            new BookIsbn($isbn)
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     */
    public function find(array $data): Book
    {
        $data = $this->bookValidator->prepare($data);
        $inputDto = new BookInputDto(title: $data['title']);

        return $this->bookRepository->find(
            $this->importer->createFromInputData(
                $inputDto,
                $this->makeAuthor(
                    new AuthorInputDto(name: $data['author'])
                )
            )
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function import(array $data): Book
    {
        $data = $this->bookValidator->prepare($data);
        $inputDto = new BookInputDto(title: $data['title']);
        // book exists?

        return $this->bookRepository->import(
            $this->importer->createFromInputData(
                $inputDto,
                $this->makeAuthor(
                    new AuthorInputDto(name: $data['author'])
                )
            )
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    private function makeAuthor(AuthorInputDto $inputDto): Author
    {
        $author = $this->authorCreator->createFromInputData($inputDto);
        $listAuthor = $this->authorRepository->find($author);
        if ($listAuthor === []) {
            return $this->authorRepository->save($author);
        }

        // @note: Логика выбора нужного автора из списка если поиск вернул больше одного.
        return current($listAuthor);
    }
}
