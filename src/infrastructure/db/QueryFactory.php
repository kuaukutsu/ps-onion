<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\interface\DbConnectionDriver;
use kuaukutsu\ps\onion\domain\interface\DbQuery;
use kuaukutsu\ps\onion\infrastructure\db\pdo\SqliteDbQuery;

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
            DbConnectionDriver::PDO_SQLITE => new SqliteDbQuery($connection),
            default => throw new DbException(
                "Connection driver '{$connection->getDriver()->value}' not supported.",
            )
        };
    }
}
