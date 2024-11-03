<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\db;

use Override;
use PHPUnit\Framework\TestCase;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\infrastructure\db\DbConnectionContainer;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionMap;
use kuaukutsu\ps\onion\infrastructure\db\DbConnection;
use kuaukutsu\ps\onion\infrastructure\db\pdo\Connection;

final class ConnectionPoolTest extends TestCase
{
    use Container;

    private ConnectionMap $pool;

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


    public function testReset(): void
    {
        $this->expectException(DbException::class);

        $this->pool->clear();
        $this->pool->get('test');
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
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->pool = self::get(ConnectionMap::class);
        $this->pool->push(
            new class implements DbConnectionContainer {
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
            new class implements DbConnectionContainer {
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
