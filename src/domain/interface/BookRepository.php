<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;

interface BookRepository
{
    /**
     * @throws RequestException
     */
    public function get(string $uuid): BookDto;

    /**
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws RequestException
     * @throws LogicException
     */
    public function import(string $title, string $author): BookDto;
}
