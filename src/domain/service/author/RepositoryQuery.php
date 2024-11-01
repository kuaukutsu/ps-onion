<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\author;

use Error;
use kuaukutsu\ps\onion\domain\entity\Author;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\infrastructure\db\QueryFactory;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

final readonly class RepositoryQuery
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

        $data = $this->queryFactory
            ->make(Author::class)
            ->fetch($query, $pk->toConditions());
        if ($data === []) {
            return null;
        }

        return (new EntityMapper(Author::class))
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

        $data = $this->queryFactory
            ->make(Author::class)
            ->fetchAll($query, $params);
        if ($data === []) {
            return [];
        }

        $list = [];
        foreach ($data as $item) {
            try {
                $model = (new EntityMapper(Author::class))
                    ->makeWithCamelCase($item);
            } catch (Error) {
                continue;
            }

            $list[$model->uuid] = $model;
        }

        return $list;
    }
}
