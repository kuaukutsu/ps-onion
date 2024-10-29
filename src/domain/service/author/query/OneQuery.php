<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\author\query;

use Error;
use kuaukutsu\ps\onion\domain\entity\Author;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\service\author\Uuid;
use kuaukutsu\ps\onion\domain\service\serialize\EntityResponse;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionPool;

final readonly class OneQuery
{
    public function __construct(private ConnectionPool $connection)
    {
    }

    /**
     * @throws NotFoundException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws Error serialize data
     */
    public function get(Uuid $pk): Author
    {
        $model = $this->find($pk);
        if ($model instanceof Author) {
            return $model;
        }

        throw new NotFoundException(
            strtr("[uuid] Author not found.", $pk->toConditions())
        );
    }

    /**
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws Error serialize data
     */
    public function find(Uuid $pk): ?Author
    {
        $query = <<<SQL
SELECT * FROM author WHERE uuid = :uuid
SQL;

        $data = $this->fetch($query, $pk->toConditions());
        if ($data === []) {
            return null;
        }

        return (new EntityResponse(Author::class))
            ->makeWithCamelCase($data);
    }

    /**
     * @param non-empty-string $query
     * @param array<string, scalar> $bindValues
     * @return array<string, mixed>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    private function fetch(string $query, array $bindValues): array
    {
        $stmt = $this->connection
            ->get(Author::class)
            ->prepare($query);

        $stmt->bindValues($bindValues);
        if ($stmt->execute() === false) {
            throw new DbStatementException($stmt->getLastError(), $query);
        }

        return $stmt->fetchAssoc();
    }
}
