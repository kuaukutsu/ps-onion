<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\application;

use Override;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\entity\author\AuthorMetadata;
use kuaukutsu\ps\onion\domain\entity\author\AuthorPerson;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\service\AuthorCreator;
use kuaukutsu\ps\onion\domain\service\BookCreator;

use function DI\factory;

trait BookSetUp
{
    use Container;

    #[Override]
    public static function setUpBeforeClass(): void
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

                    #[Override]
                    public function get(AuthorUuid $uuid): Author
                    {
                        return new Author(
                            $uuid,
                            new AuthorPerson(name: 'tester'),
                            new AuthorMetadata(),
                        );
                    }

                    #[Override]
                    public function exists(AuthorPerson $person): bool
                    {
                        return $person->name === 'Tester';
                    }

                    #[Override]
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

                    #[Override]
                    public function save(Author $author): void
                    {
                        if ($author->person->name === 'Exception') {
                            throw new InfrastructureException();
                        }
                    }
                }
            )
        );

        self::setDefinition(
            BookRepository::class,
            factory(
                fn(ContainerInterface $container): BookRepository => new readonly class (
                    $container
                ) implements BookRepository {
                    private BookCreator $creator;
                    public function __construct(ContainerInterface $container)
                    {
                        $this->creator = $container->make(BookCreator::class);
                    }

                    #[Override]
                    public function get(BookIsbn $isbn): Book
                    {
                        if ($isbn->getValue() === '0123456') {
                            throw new NotFoundException();
                        }

                        return $this->creator->createFromInputData(
                            title: new BookTitle('book.title'),
                            author: new BookAuthor('book.author'),
                        );
                    }

                    #[Override]
                    public function find(BookTitle $title, ?BookAuthor $author = null): ?Book
                    {
                        if ($title->name === 'exception') {
                            return null;
                        }

                        if ($author === null) {
                            $author = new BookAuthor('book.unknown.author');
                        }

                        return $this->creator->createFromInputData($title, $author);
                    }

                    #[Override]
                    public function import(Book $book): void
                    {
                        if ($book->title->name === 'exception') {
                            throw new InfrastructureException();
                        }
                    }
                }
            )
        );
    }
}
