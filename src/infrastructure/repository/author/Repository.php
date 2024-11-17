<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use Override;
use RuntimeException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMapper;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\db\QueryFactory;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

final readonly class Repository implements AuthorRepository
{
    public function __construct(
        private QueryFactory $query,
        private LoggerInterface $logger,
    ) {
    }

    #[Override]
    public function get(AuthorUuid $uuid): Author
    {
        $query = <<<SQL
SELECT * FROM author WHERE uuid=:uuid
SQL;

        try {
            $dto = $this->query
                ->make(Author::class)
                ->prepare($query, $uuid->toConditions())
                ->fetch(AuthorDto::class);
        } catch (DbException | DbStatementException | RuntimeException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        if ($dto instanceof AuthorDto) {
            return AuthorMapper::toModel($dto);
        }

        throw new NotFoundException(
            strtr("[uuid] Author not found.", $uuid->toConditions())
        );
    }

    #[Override]
    public function findByName(string $name): array
    {
        $query = <<<SQL
SELECT * FROM author WHERE name=:name;
SQL;

        try {
            $iterable = $this->query
                ->make(Author::class)
                ->prepare($query, ['name' => $name])
                ->fetchAll(AuthorDto::class);
        } catch (DbException | DbStatementException | RuntimeException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        $list = [];
        foreach ($iterable as $author) {
            $list[$author->uuid] = AuthorMapper::toModel($author);
        }

        return $list;
    }

    public function exists(string $name): bool
    {
        $query = <<<SQL
SELECT uuid FROM author WHERE name=:name;
SQL;

        try {
            return $this->query
                ->make(Author::class)
                ->prepare($query, ['name' => $name])
                ->exists();
        } catch (DbException | DbStatementException | RuntimeException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }
    }

    #[Override]
    public function save(Author $author): Author
    {
        $query = <<<SQL
INSERT INTO author (uuid, name, created_at, updated_at) VALUES (:uuid, :name, :created_at, :updated_at);
SQL;

        try {
             $this->query
                ->make(Author::class)
                ->execute($query, AuthorMapper::toDto($author)->toArray());
        } catch (DbException | DbStatementException | RuntimeException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        return $author;
    }
}
