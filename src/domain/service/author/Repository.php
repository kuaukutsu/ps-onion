<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\author;

use Error;
use kuaukutsu\ps\onion\domain\entity\Author;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\Application;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionMap;
use kuaukutsu\ps\onion\infrastructure\db\pdo\SqliteConnection;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

final readonly class Repository
{
    public function __construct(
        Application $application,
        ConnectionMap $connectionMap,
        private Query $query,
        private LoggerInterface $logger,
    ) {
        $connectionMap->push(
            new SqliteConnection(
                Author::class,
                "sqlite:{$application->getRuntime()}/sqlite/author.sq3"
            )
        );
    }

    /**
     * @throws NotFoundException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws Error serialize data
     */
    public function get(Uuid $pk): Author
    {
        try {
            return $this->query->get($pk);
        } catch (DbException | DbStatementException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw $exception;
        }
    }

    /**
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function findByName(string $name): array
    {
        try {
            return $this->query->findByParams(['name' => $name]);
        } catch (DbException | DbStatementException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw $exception;
        }
    }
}
