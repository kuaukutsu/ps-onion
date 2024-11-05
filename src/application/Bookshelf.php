<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use Assert\Assert;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\BookRepository;

/**
 * @api
 */
final readonly class Bookshelf
{
    public function __construct(
        private BookRepository $bookRepository,
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
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function import(string $title, string $author): BookDto
    {
        Assert::that($title)->notBlank();
        Assert::that($author)->notBlank();

        return $this->bookRepository->import($title, $author);
    }
}
