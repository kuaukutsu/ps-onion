<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db\pdo;

use Override;
use kuaukutsu\ps\onion\domain\interface\Entity;
use kuaukutsu\ps\onion\infrastructure\db\DbConnectionContainer;
use kuaukutsu\ps\onion\infrastructure\db\DbConnection;

final readonly class PgsqlConnection implements DbConnectionContainer
{
    /**
     * @param class-string<Entity> $key
     * @param non-empty-string $dsn
     * @param non-empty-string $username
     * @param non-empty-string $password
     * @param array<string, string|int> $options
     */
    public function __construct(
        private string $key,
        private string $dsn,
        private string $username,
        private string $password,
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
        $uniqueKey = $this->dsn
            . $this->username
            . $this->password
            . var_export($this->options, true);

        return hash('xxh3', $uniqueKey);
    }


    #[Override]
    public function makeConnection(): DbConnection
    {
        return new Connection(
            $this->dsn,
            $this->username,
            $this->password,
            $this->options,
        );
    }
}
