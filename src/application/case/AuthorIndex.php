<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case;

use TypeError;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\case\author\Create;
use kuaukutsu\ps\onion\application\case\author\Search;
use kuaukutsu\ps\onion\application\case\author\View;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;

/**
 * @api
 */
final readonly class AuthorIndex
{
    public function __construct(
        private Create $create,
        private Search $search,
        private View $view,
    ) {
    }

    /**
     * @param non-empty-string $uuid
     * @throws LogicException is input data not valid
     * @throws NotFoundException entity not found.
     * @throws TypeError serialize data
     * @throws InvalidArgumentException validation data
     * @throws InfrastructureException
     */
    public function get(string $uuid): AuthorDto
    {
        return $this->view->getByUuid($uuid);
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     * @throws NotFoundException
     */
    public function find(AuthorInput $input): AuthorDto
    {
        return $this->search->find($input);
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function push(AuthorInput $input): AuthorDto
    {
        return $this->create->create($input);
    }
}
