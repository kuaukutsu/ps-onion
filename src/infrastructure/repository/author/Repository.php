<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use Override;
use Generator;
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

        $dto = $this->handleQueryExeception(
            fn(): ?AuthorDto => $this->query
                ->make(Author::class)
                ->prepare($query, $uuid->toConditions())
                ->fetch(AuthorDto::class)
        );

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

        $iterable = $this->handleQueryExeception(
            fn(): Generator => $this->query
                ->make(Author::class)
                ->prepare($query, ['name' => $name])
                ->fetchAll(AuthorDto::class)
        );

        $list = [];
        foreach ($iterable as $author) {
            $list[$author->uuid] = AuthorMapper::toModel($author);
        }

        return $list;
    }

    #[Override]
    public function exists(string $name): bool
    {
        $query = <<<SQL
SELECT uuid FROM author WHERE name=:name;
SQL;

        return $this->handleQueryExeception(
            fn(): bool => $this->query
                ->make(Author::class)
                ->prepare($query, ['name' => $name])
                ->exists()
        );
    }

    #[Override]
    public function save(Author $author): Author
    {
        $query = <<<SQL
INSERT INTO author (uuid, name, created_at, updated_at) VALUES (:uuid, :name, :created_at, :updated_at);
SQL;

        $this->handleQueryExeception(
            fn(): bool => $this->query
                ->make(Author::class)
                ->execute($query, AuthorMapper::toDto($author)->toArray())
        );

        return $author;
    }

    /**
     * @template TResult
     * @param callable():TResult $fetch
     * @return TResult
     * @throws InfrastructureException
     */
    private function handleQueryExeception(callable $fetch)
    {
        try {
            return $fetch();
        } catch (DbException | DbStatementException | RuntimeException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }
    }
}
