<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

use Generator;
use TypeError;
use RuntimeException;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

interface DbQuery
{
    /**
     * @param non-empty-string $query
     * @param array<string, scalar|array|null> $bindValues
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function prepare(string $query, array $bindValues = []): self;

    /**
     * @template TDto of EntityDto
     * @param class-string<TDto> $entityDto
     * @return ?TDto
     * @throws RuntimeException if DbStatement is empty.
     * @throws TypeError denormalize data
     * @noinspection PhpDocSignatureInspection
     */
    public function fetch(string $entityDto): ?EntityDto;

    /**
     * @template TDto of EntityDto
     * @param class-string<TDto> $entityDto
     * @return Generator<TDto>
     * @throws RuntimeException if DbStatement is empty.
     */
    public function fetchAll(string $entityDto): Generator;
}
