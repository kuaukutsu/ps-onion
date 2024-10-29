<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\service\author\Repository;

final readonly class AuthorIndex
{
    public function __construct(
        private Repository $repository,
    ) {
    }

    /**
     * @throws DbException
     * @throws DbStatementException
     */
    public function get(string $name): array
    {
        return $this->repository->findByName($name);
    }
}
