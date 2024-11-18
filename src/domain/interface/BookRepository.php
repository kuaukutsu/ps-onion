<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;

interface BookRepository
{
    /**
     * @throws InfrastructureException
     */
    public function get(BookUuid $uuid): BookDto;

    /**
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function import(Book $book): Book;
}
