<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use Override;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;

use function DI\factory;

trait AuthorSetUp
{
    use Container;

    #[Override]
    protected function setUp(): void
    {
        self::setDefinition(
            AuthorRepository::class,
            factory(
                fn(ContainerInterface $container): AuthorRepository => new readonly class (
                    $container,
                ) implements AuthorRepository {
                    private AuthorCreator $creator;

                    public function __construct(ContainerInterface $container)
                    {
                        $this->creator = $container->make(AuthorCreator::class);
                    }

                    public function get(AuthorUuid $uuid): Author
                    {
                        if ($uuid->value === '30363638-6338-8863-b831-333265346631') {
                            throw new NotFoundException("[$uuid->value] Author not found.");
                        }

                        return new Author(
                            $uuid,
                            new AuthorPerson(name: 'Tester'),
                            new AuthorMetadata(),
                        );
                    }

                    public function exists(Author $author): bool
                    {
                        return $author->person->name === 'Tester';
                    }

                    public function find(AuthorPerson $person): array
                    {
                        if ($person->name === 'exception') {
                            return [];
                        }

                        $author = $this->creator->createFromInputData($person);
                        return [
                            $author->uuid->value => $author,
                        ];
                    }

                    public function save(Author $author): Author
                    {
                        if ($author->person->name === 'Exception') {
                            throw new InfrastructureException();
                        }

                        return $author;
                    }
                }
            )
        );
    }
}
