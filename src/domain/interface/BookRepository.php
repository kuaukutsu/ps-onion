<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use TypeError;
use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;

interface BookRepository
{
    /**
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws LogicException
     * @throws TypeError если получены не корректные данные
     */
    public function get(BookIsbn $isbn): Book;

    /**
     * @throws InfrastructureException
     * @throws LogicException
     * @throws TypeError если получены не корректные данные
     */
    public function find(BookTitle $title, ?BookAuthor $author = null): ?Book;

    /**
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function import(Book $book): void;
}
