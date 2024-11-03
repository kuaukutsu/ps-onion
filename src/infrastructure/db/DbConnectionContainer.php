<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use kuaukutsu\ps\onion\domain\exception\DbException;

interface DbConnectionContainer
{
    /**
     * @return non-empty-string
     */
    public function identity(): string;

    /**
     * @return non-empty-string
     */
    public function uniqueKey(): string;

    /**
     * @throws DbException
     */
    public function makeConnection(): DbConnection;
}
