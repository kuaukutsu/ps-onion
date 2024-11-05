<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use LogicException;
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
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws InfrastructureException
     * @throws LogicException
     */
    public function import(string $title, string $author): BookDto;
}
