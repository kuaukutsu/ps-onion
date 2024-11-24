<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\repository;

use Override;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionMap;
use kuaukutsu\ps\onion\infrastructure\db\QueryFactory;
use kuaukutsu\ps\onion\infrastructure\db\pdo\SqliteConnection;
use kuaukutsu\ps\onion\infrastructure\repository\author\Repository;

use function DI\autowire;
use function DI\factory;

final class AuthorRepositoryTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testSaveSuccess(): void
    {
        $repository = self::get(AuthorRepository::class);
        $creator = self::get(AuthorCreator::class);
        $author = $repository->save(
            $creator->createFromInputData(
                person: new AuthorPerson(name: 'test')
            )
        );

        self::assertEquals('Test', $author->person->name);
        self::assertTrue(
            $repository->exists($author->person)
        );

        $author = $repository->get(
            $author->uuid
        );

        self::assertEquals('Test', $author->person->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testNotExists(): void
    {
        $repository = self::get(AuthorRepository::class);
        $creator = self::get(AuthorCreator::class);
        $author = $creator->createFromInputData(
            person: new AuthorPerson(name: 'test')
        );

        self::assertFalse(
            $repository->exists($author->person)
        );
    }

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::setDefinition(
            ConnectionMap::class,
            factory(
                fn(): ConnectionMap => new ConnectionMap(
                    new SqliteConnection(
                        Author::class,
                        "sqlite::memory:"
                    )
                ),
            ),
        );

        self::setDefinition(
            AuthorRepository::class,
            autowire(Repository::class)
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    #[Override]
    protected function setUp(): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS author
(
    uuid       TEXT PRIMARY KEY,
    name       TEXT,
    created_at TEXT,
    updated_at TEXT
)
SQL;

        self::get(QueryFactory::class)
            ->make(Author::class)
            ->execute($sql);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    #[Override]
    protected function tearDown(): void
    {
        $sql = <<<SQL
DELETE FROM author;
SQL;

        self::get(QueryFactory::class)
            ->make(Author::class)
            ->execute($sql);
    }
}
