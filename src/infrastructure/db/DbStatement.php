<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

interface DbStatement
{
    public function bindValue(string $param, mixed $value, int $type): bool;

    public function execute(): bool;

    public function exists(): bool;

    /**
     * @return array<string, scalar|null>
     */
    public function fetchAssoc(): array;

    /**
     * @return array<array<string, scalar|null>>
     */
    public function fetchAssocAll(): array;

    /**
     * @return array{0: string, 1: int, 2: string}
     */
    public function getLastError(): array;
}
