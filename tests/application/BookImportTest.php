<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use Override;
use LogicException;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\BookUuidGenerator;

use function DI\factory;

final class BookImportTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportSuccess(): void
    {
        $app = self::get(Bookshelf::class);
        $domain = $app->import(['title' => 'book.test', 'author' => 'book.author']);

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
        $app->import(['title' => '']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportAuthorRequiredError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->import(['title' => 'book.test']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportValidateStructureError(): void
    {
        $this->expectException(LogicException::class);

        $app = self::get(Bookshelf::class);
        $app->import([]);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(Bookshelf::class);
        $app->import(['title' => 'exception', 'author' => 'book.author']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookImportAuthorSaveError(): void
    {
        $this->expectException(InfrastructureException::class);

        $app = self::get(Bookshelf::class);
        $app->import(['title' => 'book.test', 'author' => 'exception']);
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

                    public function find(AuthorPerson $person): array
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

        self::setDefinition(
            BookRepository::class,
            factory(
                fn(): BookRepository => new class implements BookRepository {
                    public function get(BookIsbn $isbn): Book
                    {
                        return new Book(
                            uuid: BookUuidGenerator::generateByIsbn($isbn->getValue()),
                            title: new BookTitle('book.title'),
                            author: new BookAuthor('book.author'),
                        );
                    }

                    public function find(BookTitle $title, ?BookAuthor $author = null): ?Book
                    {
                        if ($title->name === 'exception') {
                            return null;
                        }

                        if ($author === null) {
                            return null;
                        }

                        return new Book(
                            BookUuidGenerator::generate(),
                            $title,
                            $author,
                        );
                    }

                    public function import(Book $book): Book
                    {
                        if ($book->title->name === 'exception') {
                            throw new InfrastructureException();
                        }

                        return $book;
                    }
                }
            )
        );
    }
}
