<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use TypeError;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMapper;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

final readonly class AuthorRepository
{
    public function __construct(
        private AuthorRepositoryQuery $query,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws NotFoundException entity not found.
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws TypeError serialize data
     */
    public function get(AuthorUuid $uuid): Author
    {
        try {
            return AuthorMapper::toModel(
                $this->query->get($uuid)
            );
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
            $query = $this->query->findByParams(['name' => $name]);
        } catch (DbException | DbStatementException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw $exception;
        }

        $list = [];
        foreach ($query as $author) {
            $list[$author->uuid] = $author;
        }

        return $list;
    }

    /**
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function save(Author $author): Author
    {
        return $author;
    }
}
