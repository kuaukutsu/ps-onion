<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\db;

use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\interface\DbConnection;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionContainer;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionPool;
use kuaukutsu\ps\onion\infrastructure\pdo\Connection;
use kuaukutsu\ps\onion\tests\Container;

final class ConnectionPoolTest extends TestCase
{
    use Container;

    private ConnectionPool $pool;

    public function testSuccess(): void
    {
        $connectionOne = $this->pool->get('test');
        $connectionTwo = $this->pool->get('test');

        self::assertEquals($connectionOne, $connectionTwo);
    }

    public function testNotFound(): void
    {
        $this->expectException(DbException::class);

        $this->pool->get('fail');
    }

    public function testException(): void
    {
        $this->expectException(DbException::class);

        $this->pool->get('exception');
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->pool = self::get(ConnectionPool::class);
        $this->pool->push(
            new class implements ConnectionContainer {
                public function identity(): string
                {
                    return 'test';
                }

                public function uniqueKey(): string
                {
                    return 'test';
                }

                public function makeConnection(): DbConnection
                {
                    return new Connection(
                        'sqlite::memory:',
                    );
                }
            }
        );

        $this->pool->push(
            new class implements ConnectionContainer {
                public function identity(): string
                {
                    return 'exception';
                }

                public function uniqueKey(): string
                {
                    return 'exception';
                }

                public function makeConnection(): DbConnection
                {
                    throw new DbException('exception');
                }
            }
        );
    }
}
