<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\pdo;

use PDO;
use PDOException;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\interface\DbConnection;
use kuaukutsu\ps\onion\domain\interface\DbStatement;

final readonly class Connection implements DbConnection
{
    private PDO $connection;

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
    }

    public function prepare(string $query): DbStatement
    {
        return new Statement(
            $this->connection->prepare($query),
        );
    }

    public function prepareCursor(string $query): DbStatement
    {
        return new Statement(
            $this->connection->prepare($query, [PDO::CURSOR_SCROLL => PDO::ATTR_CURSOR]),
        );
    }

    public function exec(string $query): int | false
    {
        return $this->connection->exec($query);
    }

    public function getLastError(): array
    {
        /**
         * @var array{0: string, 1: int, 2: string}
         */
        return $this->connection->errorInfo();
    }
}
