<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository;

use Override;
use RuntimeException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\ApplicationSetup;
use kuaukutsu\ps\onion\infrastructure\db\QueryFactory;

final readonly class RepositorySetup implements ApplicationSetup
{
    public function __construct(
        private QueryFactory $queryFactory,
    ) {
    }

    /**
     * @throws RuntimeException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    #[Override]
    public function run(ApplicationInterface $application): void
    {
        $this->createSqliteDatabase($application->getRuntime());
        $this->migrateAuthor();
    }

    /**
     * @throws RuntimeException
     */
    private function createSqliteDatabase(string $runtimeDir): void
    {
        $databaseDir = $runtimeDir . DIRECTORY_SEPARATOR . 'sqlite';
        if (is_dir($databaseDir) === false && mkdir($databaseDir) === false) {
            throw new RuntimeException("Could not create '$databaseDir' directory.");
        }
    }

    /**
     * @throws RuntimeException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    private function migrateAuthor(): void
    {
        $fileMigrate = __DIR__ . "/author/author.sql";
        if (file_exists($fileMigrate) === false) {
            throw new RuntimeException("File '$fileMigrate' does not exist.");
        }

        /** @var non-empty-string $sql */
        $sql = file_get_contents($fileMigrate);
        $this->queryFactory
            ->make(Author::class)
            ->execute($sql);
    }
}
