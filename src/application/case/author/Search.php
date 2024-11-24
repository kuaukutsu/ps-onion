<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\author;

use LogicException;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\validator\AuthorValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMapper;
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
        private AuthorValidator $authorValidator,
    ) {
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException is read record
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
}
