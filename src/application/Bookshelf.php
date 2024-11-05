<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
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
     * @throws InfrastructureException
     * @throws InvalidArgumentException validation data
     */
    public function get(string $uuid): BookDto
    {
        $this->uuidValidator->exception($uuid);
        return $this->bookRepository->get($uuid);
    }

    /**
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function import(string $title, string $author): BookDto
    {
        // Logic: validate args (allowed types)
        assert($title !== '', 'non-empty-string');
        assert($author !== '', 'non-empty-string');

        return $this->bookRepository->import($title, $author);
    }
}
