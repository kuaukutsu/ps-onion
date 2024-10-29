<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db\pdo;

use Override;
use PDO;
use PDOException;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\interface\DbConnection;
use kuaukutsu\ps\onion\domain\interface\DbConnectionDriver;
use kuaukutsu\ps\onion\domain\interface\DbStatement;

final readonly class Connection implements DbConnection
{
    private PDO $connection;

    private DbConnectionDriver $driver;

    /**
     * @throws DbException
     */
    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        array $options = [],
    ) {
        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new DbException($e->getMessage(), (int)$e->getCode(), $e);
        }

        $this->driver = match (true) {
            str_starts_with($dsn, 'sqlite:') => DbConnectionDriver::PDO_SQLITE,
            str_starts_with($dsn, 'pgsql:') => DbConnectionDriver::PDO_PGSQL,
            default => DbConnectionDriver::UNSUPPORTED,
        };
    }

    public function getDriver(): DbConnectionDriver
    {
        return $this->driver;
    }

    #[Override]
    public function prepare(string $query): DbStatement
    {
        return new Statement(
            $this->connection->prepare($query),
        );
    }

    #[Override]
    public function prepareCursor(string $query): DbStatement
    {
        return new Statement(
            $this->connection->prepare($query, [PDO::CURSOR_SCROLL => PDO::ATTR_CURSOR]),
        );
    }

    #[Override]
    public function exec(string $query): int | false
    {
        return $this->connection->exec($query);
    }

    #[Override]
    public function getLastError(): array
    {
        /**
         * @var array{0: string, 1: int, 2: string}
         */
        return $this->connection->errorInfo();
    }
}
