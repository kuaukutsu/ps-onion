<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\pdo;

use PDO;
use PDOStatement;
use kuaukutsu\ps\onion\domain\interface\DbStatement;

final readonly class Statement implements DbStatement
{
    public function __construct(private PDOStatement $statement)
    {
    }

    public function bindValues(array $bindValues): void
    {
        $makeValueType = static function (mixed $value): int {
            return is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
        };

        foreach ($bindValues as $key => $value) {
            if (is_array($value)) {
                $row = 0;
                foreach ($value as $itemValue) {
                    $this->statement->bindValue(
                        ':' . $key . $row,
                        $itemValue,
                        $makeValueType($itemValue),
                    );
                    $row++;
                }
            } else {
                $this->statement->bindValue(
                    ':' . $key,
                    $value,
                    $makeValueType($value)
                );
            }
        }
    }

    public function execute(): bool
    {
        return $this->statement->execute();
    }

    public function fetchAssoc(): array
    {
        $data = $this->statement->fetch(PDO::FETCH_ASSOC);
        if (is_array($data)) {
            return $data;
        }

        return [];
    }

    public function fetchAssocAll(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getLastError(): array
    {
        /**
         * @var array{0: string, 1: int, 2: string}
         */
        return $this->statement->errorInfo();
    }
}
