<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\author\query;

use Error;
use kuaukutsu\ps\onion\domain\entity\Author;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\service\serialize\EntityResponse;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionPool;

final readonly class FindQuery
{
    public function __construct(private ConnectionPool $connection)
    {
    }

    /**
     * @param array<string, scalar> $params
     * @return array<string, Author>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function find(array $params): array
    {
        $conditions = '';
        foreach (array_keys($params) as $key) {
            if ($conditions !== '') {
                $conditions .= ' AND ';
            }

            $conditions .= $key . " = :" . $key;
        }

        $query = <<<SQL
SELECT * FROM author WHERE $conditions;
SQL;

        $data = $this->fetchAll($query, $params);
        if ($data === []) {
            return [];
        }

        $list = [];
        foreach ($data as $item) {
            try {
                $model = (new EntityResponse(Author::class))
                    ->makeWithCamelCase($item);
            } catch (Error) {
                continue;
            }

            $list[$model->uuid] = $model;
        }

        return $list;
    }

    /**
     * @param non-empty-string $query
     * @param array<string, scalar> $bindValues
     * @return array<array<string, mixed>>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    private function fetchAll(string $query, array $bindValues): array
    {
        $stmt = $this->connection
            ->get(Author::class)
            ->prepare($query);

        $stmt->bindValues($bindValues);
        if ($stmt->execute() === false) {
            throw new DbStatementException($stmt->getLastError(), $query);
        }

        return $stmt->fetchAssocAll();
    }
}
