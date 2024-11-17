<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use TypeError;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\validator\AuthorValidator;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;

/**
 * @api
 */
final readonly class AuthorIndex
{
    public function __construct(
        private AuthorCreator $creator,
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
    public function get(string $uuid): Author
    {
        $this->uuidValidator->exception($uuid);
        return $this->repository->get(
            new AuthorUuid($uuid)
        );
    }

    /**
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function push(array $data): Author
    {
        $prepareData = $this->authorValidator->prepare($data);
        if ($this->repository->exists($prepareData['name'])) {
            throw new LogicException("Author '{$prepareData['name']}' already exists.");
        }

        return $this->repository->save(
            $this->creator->createFromRawData(
                $prepareData['name'],
            )
        );
    }
}
