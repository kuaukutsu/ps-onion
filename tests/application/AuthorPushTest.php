<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\application\case\AuthorIndex;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\domain\exception\ConflictException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;

final class AuthorPushTest extends TestCase
{
    use AuthorSetUp;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushSuccess(): void
    {
        $app = self::get(AuthorIndex::class);
        $domain = $app->push(
            new AuthorInput(name: 'test'),
        );

        self::assertEquals('Test', $domain->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(AuthorIndex::class);
        $app->push(
            new AuthorInput(name: ' '),
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushExistsError(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage("Author 'Tester' already exists.");

        $app = self::get(AuthorIndex::class);
        $app->push(
            new AuthorInput(name: 'tester'),
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorPushSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(AuthorIndex::class);
        $app->push(
            new AuthorInput(name: 'exception')
        );
    }
}
