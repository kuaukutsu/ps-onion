<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\infrastructure\db\pdo\SqliteQuery;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain\service
 */
final readonly class QueryFactory
{
    public function __construct(private ConnectionMap $map)
    {
    }

    /**
     * @throws DbException
     */
    public function make(string $connectionIdentity): DbQuery
    {
        $connection = $this->map->get($connectionIdentity);
        return match ($connection->getDriver()) {
            DbConnectionDriver::PDO_SQLITE => new SqliteQuery($connection),
            default => throw new DbException(
                "Connection driver '{$connection->getDriver()->value}' not supported.",
            )
        };
    }
}
