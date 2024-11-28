<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case\author;

use TypeError;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\validator\AuthorValidator;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\author\AuthorDto;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class View
{
    public function __construct(
        private AuthorRepository $repository,
        private AuthorValidator $validator,
        private UuidValidator $uuidValidator,
        private AuthorMapper $mapper,
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
    public function getByUuid(string $uuid): AuthorDto
    {
        $this->uuidValidator->exception($uuid);
        return $this->mapper->toDto(
            $this->repository->get(
                new AuthorUuid($uuid)
            )
        );
    }

    /**
     * @throws LogicException
     * @throws InfrastructureException
     */
    public function exists(AuthorInput $input): bool
    {
        return $this->repository->exists(
            $this->validator->prepare($input)
        );
    }
}
