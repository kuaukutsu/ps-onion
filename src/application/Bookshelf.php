<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\application\validator\BookValidator;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\BookImporter;

/**
 * @api
 */
final readonly class Bookshelf
{
    public function __construct(
        private BookImporter $importer,
        private BookRepository $bookRepository,
        private BookValidator $bookValidator,
        private UuidValidator $uuidValidator,
    ) {
    }

    /**
     * @param non-empty-string $uuid
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     * @throws InvalidArgumentException validation data
     */
    public function get(string $uuid): BookDto
    {
        $this->uuidValidator->exception($uuid);
        return $this->bookRepository->get(
            new BookUuid($uuid)
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function import(array $data): Book
    {
        $prepareData = $this->bookValidator->prepare($data);
        return $this->bookRepository->import(
            $this->importer->createFromRawData(
                $prepareData['title'],
                $prepareData['author'],
            )
        );
    }
}
