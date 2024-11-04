<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use kuaukutsu\ps\onion\domain\exception\DbException;

final class ConnectionMap
{
    /**
     * @var array<string, DbConnectionContainer>
     */
    private array $map = [];

    /**
     * @var array<string, DbConnection>
     */
    private array $connections = [];

    public function __construct(
        DbConnectionContainer ...$containers,
    ) {
        foreach ($containers as $container) {
            $this->push($container);
        }
    }

    /**
     * @throws DbException
     */
    public function get(string $identity, bool $reset = false): DbConnection
    {
        if ($this->map === [] || array_key_exists($identity, $this->map) === false) {
            throw new DbException("Connection $identity does not exist.");
        }

        return $this->makeConnection($this->map[$identity], $reset);
    }

    public function push(DbConnectionContainer $connection): void
    {
        $this->map[$connection->identity()] = $connection;
    }

    public function clear(): void
    {
        $this->map = [];
        $this->connections = [];
    }

    /**
     * @throws DbException
     */
    private function makeConnection(DbConnectionContainer $connection, bool $reset): DbConnection
    {
        if ($reset || array_key_exists($connection->uniqueKey(), $this->connections) === false) {
            $this->connections[$connection->uniqueKey()] = $connection->makeConnection();
        }

        return $this->connections[$connection->uniqueKey()];
    }
}
