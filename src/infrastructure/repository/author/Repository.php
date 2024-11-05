<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use Override;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMapper;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

final readonly class Repository implements AuthorRepository
{
    public function __construct(
        private RepositoryQuery $query,
        private LoggerInterface $logger,
    ) {
    }

    #[Override]
    public function get(AuthorUuid $uuid): Author
    {
        try {
            return AuthorMapper::toModel(
                $this->query->get($uuid)
            );
        } catch (DbException | DbStatementException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw $exception;
        }
    }

    #[Override]
    public function findByName(string $name): array
    {
        try {
            $query = $this->query->findByParams(['name' => $name]);
        } catch (DbException | DbStatementException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception),
                __METHOD__,
            );

            throw $exception;
        }

        $list = [];
        foreach ($query as $author) {
            $list[$author->uuid] = AuthorMapper::toModel($author);
        }

        return $list;
    }

    #[Override]
    public function save(Author $author): Author
    {
        return $author;
    }
}
