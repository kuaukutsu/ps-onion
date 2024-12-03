<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\author;

use LogicException;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\output\AuthorDto;
use kuaukutsu\ps\onion\application\validator\AuthorValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\service\AuthorSearch;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class Search
{
    public function __construct(
        private AuthorSearch $search,
        private AuthorRepository $repository,
        private AuthorValidator $validator,
    ) {
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException is read record
     * @throws NotFoundException
     */
    public function find(AuthorInput $input): AuthorDto
    {
        $person = $this->validator->prepare($input);
        $author = $this->search->find(
            $this->repository->find($person)
        );

        if ($author instanceof Author) {
            return AuthorDto::fromEntity($author);
        }

        throw new NotFoundException("Author '$person->name' not found.");
    }
}
