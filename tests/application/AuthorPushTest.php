<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use Override;
use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\application\AuthorIndex;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;

use function DI\factory;

final class AuthorPushTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushSuccess(): void
    {
        $app = self::get(AuthorIndex::class);
        $domain = $app->push(['name' => 'test']);

        self::assertEquals('Test', $domain->person->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(AuthorIndex::class);
        $app->push(['name' => '']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushValidateStructureError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(AuthorIndex::class);
        $app->push([]);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushExistsError(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Author 'Tester' already exists.");

        $app = self::get(AuthorIndex::class);
        $app->push(['name' => 'tester']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(AuthorIndex::class);
        $app->push(['name' => 'exception']);
    }

    #[Override]
    protected function setUp(): void
    {
        self::setDefinition(
            AuthorRepository::class,
            factory(
                fn(): AuthorRepository => new class implements AuthorRepository {
                    public function get(AuthorUuid $uuid): Author
                    {
                        return new Author(
                            $uuid,
                            new AuthorPerson(name: 'tester'),
                            new AuthorMetadata()
                        );
                    }

                    public function exists(Author $author): bool
                    {
                        return $author->person->name === 'Tester';
                    }

                    public function find(Author $author): array
                    {
                        return [];
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
