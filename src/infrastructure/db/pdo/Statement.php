<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db\pdo;

use Override;
use PDO;
use PDOStatement;
use kuaukutsu\ps\onion\infrastructure\db\DbStatement;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\db
 */
final readonly class Statement implements DbStatement
{
    public function __construct(private PDOStatement $statement)
    {
    }

    #[Override]
    public function bindValue(string $param, mixed $value, int $type): bool
    {
        return $this->statement->bindValue($param, $value, $type);
    }

    #[Override]
    public function execute(): bool
    {
        return $this->statement->execute();
    }

    #[Override]
    public function fetchAssoc(): array
    {
        $data = $this->statement->fetch(PDO::FETCH_ASSOC);
        if (is_array($data)) {
            return $data;
        }

        return [];
    }

    #[Override]
    public function fetchAssocAll(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    #[Override]
    public function getLastError(): array
    {
        /**
         * @var array{0: string, 1: int, 2: string}
         */
        return $this->statement->errorInfo();
    }
}
