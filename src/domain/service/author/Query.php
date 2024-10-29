<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\author;

use Error;
use kuaukutsu\ps\onion\domain\entity\Author;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\service\serialize\EntityResponse;
use kuaukutsu\ps\onion\infrastructure\db\QueryFactory;

final readonly class Query
{
    public function __construct(private QueryFactory $queryFactory)
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

        $data = $this->fetchOne($query, $pk->toConditions());
        if ($data === []) {
            return null;
        }

        return (new EntityResponse(Author::class))
            ->makeWithCamelCase($data);
    }

    /**
     * @param array<string, scalar> $params
     * @return array<string, Author>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function findByParams(array $params): array
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
     * @return array<string, mixed>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    private function fetchOne(string $query, array $bindValues): array
    {
        return $this->queryFactory
            ->makeQuery(Author::class)
            ->fetch($query, $bindValues);
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
        return $this->queryFactory
            ->makeQuery(Author::class)
            ->fetchAll($query, $bindValues);
    }
}
