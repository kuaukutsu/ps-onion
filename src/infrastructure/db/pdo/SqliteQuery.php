<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db\pdo;

use Error;
use Override;
use Generator;
use RuntimeException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\infrastructure\db\DbConnection;
use kuaukutsu\ps\onion\infrastructure\db\DbQuery;
use kuaukutsu\ps\onion\infrastructure\db\DbStatement;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\db
 */
final readonly class SqliteQuery implements DbQuery
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?DbStatement $statement; // @phpstan-ignore-line

    public function __construct(
        private DbConnection $connection,
    ) {
    }

    #[Override]
    public function prepare(string $query, array $bindValues = []): self
    {
        $self = clone $this;
        $self->statement = $this->prepareStatement($query, $bindValues); // @phpstan-ignore-line
        return $self;
    }

    #[Override]
    public function fetch(string $entityDto): ?EntityDto
    {
        if (isset($this->statement) === false) {
            throw new RuntimeException(
                'Statement is empty. Need to call “prepare”.'
            );
        }

        $data = $this->statement->fetchAssoc();
        if ($data === []) {
            return null;
        }

        return EntityMapper::denormalize($entityDto, $data);
    }

    #[Override]
    public function fetchAll(string $entityDto): Generator
    {
        if (isset($this->statement) === false) {
            throw new RuntimeException(
                'Statement is empty. Need to call “prepare”.'
            );
        }

        foreach ($this->statement->fetchAssocAll() as $item) {
            try {
                yield EntityMapper::denormalize($entityDto, $item);
            } catch (Error) {
                continue;
            }
        }
    }

    /**
     * @param non-empty-string $query
     * @param array<string, scalar|array|null> $bindValues
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    private function prepareStatement(string $query, array $bindValues): DbStatement
    {
        $stmt = $this->connection
            ->prepare($query);

        $this->bindValues($stmt, $bindValues);
        if ($stmt->execute() === false) {
            throw new DbStatementException($stmt->getLastError(), $query);
        }

        return $stmt;
    }

    /**
     * @param array<string, scalar|array|null> $bindValues
     */
    private function bindValues(DbStatement $statement, array $bindValues): void
    {
        $makeValueType = static function (mixed $value): int {
            return is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
        };

        foreach ($bindValues as $key => $value) {
            if (is_array($value)) {
                $row = 0;
                foreach ($value as $itemValue) {
                    $statement->bindValue(
                        ':' . $key . $row,
                        $itemValue,
                        $makeValueType($itemValue),
                    );
                    $row++;
                }
            } else {
                $statement->bindValue(
                    ':' . $key,
                    $value,
                    $makeValueType($value)
                );
            }
        }
    }
}
