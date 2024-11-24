<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\repository;

use Override;
use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\application\decorator\ClientDecorator;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\BookCreator;
use kuaukutsu\ps\onion\infrastructure\repository\book\Repository;

use function DI\autowire;
use function DI\factory;

final class BookRepositoryTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookSave(): void
    {
        $repository = self::get(BookRepository::class);
        $creator = self::get(BookCreator::class);
        $book = $repository->import(
            $creator->createFromInputData(
                new BookTitle(name: 'test'),
                new BookAuthor(name: 'tester'),
            )
        );

        self::assertInstanceOf(Book::class, $book);
    }

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::setDefinition(
            PsrClientInterface::class,
            factory(
                fn(): PsrClientInterface => new class implements PsrClientInterface {
                    public function sendRequest(RequestInterface $request): ResponseInterface
                    {
                        return new Response(
                            200,
                            [
                                'Content-Type' => ['application/json'],
                            ],
                            $request->getBody()->getContents()
                        );
                    }
                }
            ),
        );

        self::setDefinition(
            ClientInterface::class,
            factory(
                fn(ContainerInterface $container): ClientInterface => new ClientDecorator($container)
            )
        );

        self::setDefinition(
            BookRepository::class,
            autowire(Repository::class)
        );
    }
}
