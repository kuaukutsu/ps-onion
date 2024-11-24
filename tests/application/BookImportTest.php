<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;

final class BookImportTest extends TestCase
{
    use BookSetUp;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportSuccess(): void
    {
        $app = self::get(Bookshelf::class);
        $domain = $app->import(
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
    public function testBookImportValidateValueError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->import(
            new BookInput(
                title: ' ',
                author: new AuthorInput(name: 'book.author'),
            )
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportAuthorRequiredError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->import(
            new BookInput(
                title: 'book.test',
            )
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(Bookshelf::class);
        $app->import(
            new BookInput(
                title: 'exception',
                author: new AuthorInput(name: 'book.author'),
            )
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportAuthorSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(Bookshelf::class);
        $app->import(new BookInput(
            title: 'book.test',
            author: new AuthorInput(name: 'exception'),
        ));
    }
}
