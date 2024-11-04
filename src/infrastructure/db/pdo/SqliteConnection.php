<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db\pdo;

use Override;
use kuaukutsu\ps\onion\domain\interface\Entity;
use kuaukutsu\ps\onion\infrastructure\db\DbConnectionContainer;
use kuaukutsu\ps\onion\infrastructure\db\DbConnection;

final readonly class SqliteConnection implements DbConnectionContainer
{
    /**
     * @param class-string<Entity> $key
     * @param non-empty-string $dsn
     * @param array<string, string|int> $options
     */
    public function __construct(
        private string $key,
        private string $dsn,
        private array $options = [],
    ) {
    }

    #[Override]
    public function identity(): string
    {
        return $this->key;
    }

    #[Override]
    public function uniqueKey(): string
    {
        return hash('xxh3', $this->dsn);
    }


    #[Override]
    public function makeConnection(): DbConnection
    {
        return new Connection($this->dsn, null, null, $this->options);
    }
}
