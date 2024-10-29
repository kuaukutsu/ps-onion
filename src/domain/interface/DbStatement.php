<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface DbStatement
{
    /**
     * @param array<string, scalar|array|null> $bindValues
     */
    public function bindValues(array $bindValues): void;

    public function execute(): bool;

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
