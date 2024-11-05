<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use TypeError;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;

interface AuthorRepository
{
    /**
     * @throws NotFoundException entity not found.
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws TypeError serialize data
     */
    public function get(AuthorUuid $uuid): Author;

    /**
     * @return array<string, Author>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function findByName(string $name): array;

    /**
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function save(Author $author): Author;
}
