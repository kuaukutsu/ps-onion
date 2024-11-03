<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;

interface DbQuery
{
    /**
     * @param non-empty-string $query
     * @param array<string, scalar|array|null> $bindValues
     * @return array<string, mixed>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function fetch(string $query, array $bindValues = []): array;

        /**
         * @param non-empty-string $query
         * @param array<string, scalar|array|null> $bindValues
         * @return array<array<string, mixed>>
         * @throws DbException connection failed.
         * @throws DbStatementException query failed.
         */
    public function fetchAll(string $query, array $bindValues = []): array;
}
