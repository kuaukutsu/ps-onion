<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\application\case\Bookshelf;
use kuaukutsu\ps\onion\domain\exception\NotFoundException as NotFoundExceptionDomain;

final class BookViewTest extends TestCase
{
    use BookSetUp;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookGetSuccess(): void
    {
        $app = self::get(Bookshelf::class);
        $domain = $app->get('0123456789');

        self::assertEquals('book.title', $domain->title);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookGetValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->get(' ');
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookGetValidateTypeError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->get('www');
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookGetNotFoundError(): void
    {
        $this->expectException(NotFoundExceptionDomain::class);

        $app = self::get(Bookshelf::class);
        $app->get('0123456');
    }
}
