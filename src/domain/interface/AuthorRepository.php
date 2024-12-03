<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use TypeError;
use LogicException;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;

interface AuthorRepository
{
    /**
     * @throws NotFoundException entity not found.
     * @throws LogicException is data not valid
     * @throws TypeError serialize data
     * @throws InfrastructureException
     */
    public function get(AuthorUuid $uuid): Author;

    /**
     * @throws InfrastructureException
     */
    public function exists(AuthorPerson $person): bool;

    /**
     * @return array<string, Author>
     * @throws InfrastructureException
     */
    public function find(AuthorPerson $person): array;

    /**
     * @throws InfrastructureException
     */
    public function save(Author $author): void;
}
