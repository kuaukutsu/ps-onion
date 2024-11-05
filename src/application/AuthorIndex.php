<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use TypeError;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;

/**
 * @api
 */
final readonly class AuthorIndex
{
    public function __construct(
        private AuthorRepository $repository,
        private UuidValidator $uuidValidator,
    ) {
    }

    /**
     * @param non-empty-string $uuid
     * @throws NotFoundException entity not found.
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     * @throws TypeError serialize data
     * @throws InvalidArgumentException
     */
    public function get(string $uuid): Author
    {
        $this->uuidValidator->exception($uuid);
        return $this->repository->get(new AuthorUuid($uuid));
    }
}
