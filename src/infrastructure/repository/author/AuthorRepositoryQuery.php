<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use Error;
use TypeError;
use Generator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\infrastructure\db\QueryFactory;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 */
final readonly class AuthorRepositoryQuery
{
    public function __construct(private QueryFactory $queryFactory)
    {
    }

    /**
     * @throws NotFoundException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws TypeError serialize data
     */
    public function get(AuthorUuid $pk): AuthorDto
    {
        $model = $this->find($pk);
        if ($model instanceof AuthorDto) {
            return $model;
        }

        throw new NotFoundException(
            strtr("[uuid] Author not found.", $pk->toConditions())
        );
    }

    /**
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws TypeError serialize data
     */
    public function find(AuthorUuid $pk): ?AuthorDto
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

        return EntityMapper::denormalize(AuthorDto::class, $data);
    }

    /**
     * @param array<string, scalar> $params
     * @return Generator<AuthorDto>
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function findByParams(array $params): Generator
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
            return null;
        }

        foreach ($data as $item) {
            try {
                yield EntityMapper::denormalize(AuthorDto::class, $item);
            } catch (Error) {
                continue;
            }
        }

        return null;
    }
}
