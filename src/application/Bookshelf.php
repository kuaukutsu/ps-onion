<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\Book;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\domain\service\book\Repository;

/**
 * @api
 */
final readonly class Bookshelf
{
    public function __construct(
        private Repository $bookRepository,
        private UuidValidator $uuidValidator,
    ) {
    }

    /**
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function get(string $uuid): Book
    {
        $this->uuidValidator->exception($uuid);
        return $this->bookRepository->get($uuid);
    }

    /**
     * @throws RequestException
     * @throws LogicException
     */
    public function import(string $title, string $author): Book
    {
        // Logic: validate args (allowed types)
        assert($title !== '', 'non-empty-string');
        assert($author !== '', 'non-empty-string');

        return $this->bookRepository->import($title, $author);
    }
}
