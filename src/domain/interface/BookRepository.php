<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;

interface BookRepository
{
    /**
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function get(BookIsbn $isbn): Book;

    /**
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function find(Book $book): Book;

    /**
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function import(Book $book): Book;
}
