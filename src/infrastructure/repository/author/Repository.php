<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use Override;
use Generator;
use RuntimeException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
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
            fn(): ?RecordData => $this->query
                ->make(Author::class)
                ->prepare($query, $uuid->toConditions())
                ->fetch(RecordData::class)
        );

        if ($dto instanceof RecordData) {
            return $this->castToEntity($dto);
        }

        throw new NotFoundException(
            strtr("[uuid] Author not found.", $uuid->toConditions())
        );
    }

    #[Override]
    public function find(AuthorPerson $person): array
    {
        $query = <<<SQL
SELECT * FROM author WHERE lower(name)=lower(:name);
SQL;

        $iterable = $this->handleQueryExeception(
            fn(): Generator => $this->query
                ->make(Author::class)
                ->prepare($query, ['name' => $person->name])
                ->fetchAll(RecordData::class)
        );

        $list = [];
        foreach ($iterable as $record) {
            $list[$record->uuid] = $this->castToEntity($record);
        }

        return $list;
    }

    #[Override]
    public function exists(AuthorPerson $person): bool
    {
        $query = <<<SQL
SELECT uuid FROM author WHERE lower(name)=lower(:name);
SQL;

        return $this->handleQueryExeception(
            fn(): bool => $this->query
                ->make(Author::class)
                ->prepare($query, ['name' => $person->name])
                ->exists()
        );
    }

    #[Override]
    public function save(Author $author): void
    {
        $query = <<<SQL
INSERT INTO author (uuid, name, created_at, updated_at) VALUES (:uuid, :name, :created_at, :updated_at);
SQL;

        $this->handleQueryExeception(
            fn(): bool => $this->query
                ->make(Author::class)
                ->execute($query, $this->castToData($author)->toArray())
        );
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

    private function castToEntity(RecordData $data): Author
    {
        return new Author(
            uuid: new AuthorUuid($data->uuid),
            person: new AuthorPerson($data->name),
            metadata: new AuthorMetadata($data->createdAt, $data->updatedAt)
        );
    }

    private function castToData(Author $author): RecordData
    {
        return new RecordData(
            uuid: $author->uuid->value,
            name: $author->person->name,
            createdAt: $author->metadata->createdAt,
            updatedAt: $author->metadata->updatedAt,
        );
    }
}
