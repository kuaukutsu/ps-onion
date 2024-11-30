<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\author;

use LogicException;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\output\AuthorMapper;
use kuaukutsu\ps\onion\application\validator\AuthorValidator;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\exception\ConflictException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;
use kuaukutsu\ps\onion\domain\service\AuthorSearch;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class Create
{
    public function __construct(
        private AuthorCreator $creator,
        private AuthorSearch $search,
        private AuthorRepository $repository,
        private AuthorValidator $validator,
        private AuthorMapper $mapper,
    ) {
    }

    /**
     * @throws LogicException is input data not valid
     * @throws ConflictException is record already exists
     * @throws InfrastructureException is written record error
     */
    public function create(AuthorInput $input): AuthorDto
    {
        $author = $this->creator->createFromInputData(
            $this->validator->prepare($input)
        );

        if ($this->repository->exists($author->person)) {
            throw new ConflictException("Author '{$author->person->name}' already exists.");
        }

        return $this->mapper->toDto(
            $this->repository->save($author)
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException is written record error
     */
    public function createIfNotExists(AuthorInput $input): AuthorDto
    {
        $person = $this->validator->prepare($input);
        $author = $this->search->find(
            $this->repository->find($person),
        );

        if ($author === null) {
            $author = $this->repository->save(
                $this->creator->createFromInputData($person)
            );
        }

        return $this->mapper->toDto($author);
    }
}
