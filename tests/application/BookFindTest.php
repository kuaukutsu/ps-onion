<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
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
        $domain = $app->find(
            new BookInput(
                title: 'book.test',
                author: new AuthorInput(name: 'book.author'),
            )
        );

        self::assertEquals('book.test', $domain->title);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindAuthorNotFoundSucces(): void
    {
        $app = self::get(Bookshelf::class);
        $domain = $app->find(
            new BookInput(
                title: 'book.test',
                author: new AuthorInput(name: 'exception'),
            )
        );

        self::assertEquals('book.test', $domain->title);
        self::assertEquals('book.unknown.author', $domain->author);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFindNotFoundError(): void
    {
        $this->expectException(NotFoundExceptionDomain::class);

        $app = self::get(Bookshelf::class);
        $app->find(
            new BookInput(
                title: 'exception',
                author: new AuthorInput(name: 'book.author'),
            )
        );
    }
}
