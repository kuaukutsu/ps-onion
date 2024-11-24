<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use TypeError;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\validator\AuthorValidator;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMapper;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;
use kuaukutsu\ps\onion\domain\service\AuthorSearch;

/**
 * @api
 */
final readonly class AuthorIndex
{
    public function __construct(
        private AuthorCreator $creator,
        private AuthorSearch $search,
        private AuthorRepository $repository,
        private UuidValidator $uuidValidator,
        private AuthorValidator $authorValidator,
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
        $this->uuidValidator->exception($uuid);
        return AuthorMapper::toDto(
            $this->repository->get(
                new AuthorUuid($uuid)
            )
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     * @throws NotFoundException
     */
    public function find(AuthorInput $input): AuthorDto
    {
        $person = $this->authorValidator->prepare($input);
        $author = $this->search->find(
            $this->repository->find($person)
        );

        if ($author instanceof Author) {
            return AuthorMapper::toDto($author);
        }

        throw new NotFoundException("Author '$person->name' not found.");
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function push(AuthorInput $input): AuthorDto
    {
        $author = $this->creator->createFromInputData(
            $this->authorValidator->prepare($input)
        );

        if ($this->repository->exists($author)) {
            throw new LogicException("Author '{$author->person->name}' already exists.");
        }

        return AuthorMapper::toDto(
            $this->repository->save($author)
        );
    }
}
