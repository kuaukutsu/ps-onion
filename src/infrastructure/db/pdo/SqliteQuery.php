<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db\pdo;

use Override;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\infrastructure\db\DbConnection;
use kuaukutsu\ps\onion\infrastructure\db\DbQuery;
use kuaukutsu\ps\onion\infrastructure\db\DbStatement;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\db
 */
final readonly class SqliteQuery implements DbQuery
{
    public function __construct(private DbConnection $connection)
    {
    }

    #[Override]
    public function fetch(string $query, array $bindValues = []): array
    {
        return $this->statement($query, $bindValues)
            ->fetchAssoc();
    }

    #[Override]
    public function fetchAll(string $query, array $bindValues = []): array
    {
        return $this->statement($query, $bindValues)
            ->fetchAssocAll();
    }

    /**
     * @param non-empty-string $query
     * @param array<string, scalar|array|null> $bindValues
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    private function statement(string $query, array $bindValues): DbStatement
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
