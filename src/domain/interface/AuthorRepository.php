<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use TypeError;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;

interface AuthorRepository
{
    /**
     * @throws NotFoundException entity not found.
     * @throws TypeError serialize data
     * @throws InfrastructureException
     */
    public function get(AuthorUuid $uuid): Author;

    /**
     * @return array<string, Author>
     * @throws InfrastructureException
     */
    public function findByName(string $name): array;

    /**
     * @throws InfrastructureException
     */
    public function save(Author $author): Author;
}
