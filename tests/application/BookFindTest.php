<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException as NotFoundExceptionDomain;

final class BookFindTest extends TestCase
{
    use BookSetUp;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindSuccess(): void
    {
        $app = self::get(Bookshelf::class);
        $domain = $app->find(['title' => 'book.test', 'author' => 'book.author']);

        self::assertEquals('book.test', $domain->title);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindNotFoundError(): void
    {
        $this->expectException(NotFoundExceptionDomain::class);

        $app = self::get(Bookshelf::class);
        $app->find(['title' => 'exception', 'author' => 'book.author']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->find([]);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindValidateTypeError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->find(['title' => 'book.test']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindAuthorSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(Bookshelf::class);
        $app->import(['title' => 'book.test', 'author' => 'exception']);
    }
}
