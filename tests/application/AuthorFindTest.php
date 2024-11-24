<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\ps\onion\application\case\AuthorIndex;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\domain\exception\NotFoundException as NotFoundExceptionDomain;
use LogicException;
use PHPUnit\Framework\TestCase;

final class AuthorFindTest extends TestCase
{
    use AuthorSetUp;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorFindSuccess(): void
    {
        $app = self::get(AuthorIndex::class);
        $domain = $app->find(
            new AuthorInput(name: 'test'),
        );

        self::assertEquals('Test', $domain->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorFindValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(AuthorIndex::class);
        $app->find(
            new AuthorInput(name: ' '),
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testAuthorFindNotFoundError(): void
    {
        $this->expectException(NotFoundExceptionDomain::class);
        $this->expectExceptionMessage("Author 'exception' not found.");

        $app = self::get(AuthorIndex::class);
        $app->find(
            new AuthorInput(name: 'exception'),
        );
    }
}
