<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface DbConnection
{
    public function getDriver(): DbConnectionDriver;

    /**
     * @param non-empty-string $query
     */
    public function prepare(string $query): DbStatement;

    /**
     * @param non-empty-string $query
     */
    public function prepareCursor(string $query): DbStatement;

    /**
     * @param non-empty-string $query
     */
    public function exec(string $query): int | false;

    /**
     * @return array{0: string, 1: int, 2: string}
     */
    public function getLastError(): array;
}
