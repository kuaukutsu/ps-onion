<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\interface\DbConnection;

final class ConnectionPool
{
    /**
     * @var array<string, ConnectionContainer>
     */
    private array $map = [];

    /**
     * @var array<string, DbConnection>
     */
    private array $connections = [];

    /**
     * @throws DbException
     */
    public function get(string $key, bool $reconnect = false): DbConnection
    {
        if ($this->map === [] || array_key_exists($key, $this->map) === false) {
            throw new DbException("Connection $key does not exist.");
        }

        return $this->makeConnection($this->map[$key], $reconnect);
    }

    public function push(ConnectionContainer $connection): void
    {
        $this->map[$connection->identity()] = $connection;
    }

    /**
     * @throws DbException
     */
    private function makeConnection(ConnectionContainer $connection, bool $reconnect): DbConnection
    {
        if ($reconnect || array_key_exists($connection->uniqueKey(), $this->connections) === false) {
            $this->connections[$connection->uniqueKey()] = $connection->makeConnection();
        }

        return $this->connections[$connection->uniqueKey()];
    }
}
