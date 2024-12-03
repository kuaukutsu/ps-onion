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
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException as NotFoundExceptionDomain;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\service\BookCreator;
use kuaukutsu\ps\onion\infrastructure\http\decode\StreamJson;
use kuaukutsu\ps\onion\infrastructure\repository\book\Repository;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityJson;

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
        $book = $creator->createFromInputData(
            new BookTitle(name: 'test'),
            new BookAuthor(name: 'tester'),
        );

        $repository->import($book);

        self::assertEquals('tester', $book->author->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookSaveException(): void
    {
        $this->expectException(InfrastructureException::class);

        $repository = self::get(BookRepository::class);
        $creator = self::get(BookCreator::class);
        $book = $creator->createFromInputData(
            new BookTitle(name: 'test'),
            new BookAuthor(name: 'exception'),
        );

        $repository->import($book);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookView(): void
    {
        $repository = self::get(BookRepository::class);
        $book = $repository->get(
            new BookIsbn('987654321')
        );

        self::assertEquals('test', $book->title->name);
        self::assertEquals('tester', $book->author->name);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookViewExcepton(): void
    {
        $this->expectException(InfrastructureException::class);

        $repository = self::get(BookRepository::class);
        $repository->get(
            new BookIsbn('500')
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookViewNotFound(): void
    {
        $this->expectException(NotFoundExceptionDomain::class);

        $repository = self::get(BookRepository::class);
        $repository->get(
            new BookIsbn('404')
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testBookFind(): void
    {
        $repository = self::get(BookRepository::class);
        $book = $repository->find(
            new BookTitle(name: 'test')
        );

        self::assertNotNull($book);
    }

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::setDefinition(
            PsrClientInterface::class,
            factory(
                fn(): PsrClientInterface => new readonly class implements PsrClientInterface {
                    public function sendRequest(RequestInterface $request): ResponseInterface
                    {
                        return match (true) {
                            $request->getMethod() === 'POST' => $this->caseImport($request),
                            default => $this->caseGet($request),
                        };
                    }

                    private function caseGet(RequestInterface $request): ResponseInterface
                    {
                        if (str_contains($request->getUri()->getQuery(), '500')) {
                            return new Response(500);
                        }

                        if (str_contains($request->getUri()->getQuery(), '404')) {
                            return new Response(
                                200,
                                ['Content-Type' => ['application/json']],
                                EntityJson::encode(
                                    [
                                        'numFound' => 0,
                                        'docs' => [],
                                    ]
                                ),
                            );
                        }

                        return new Response(
                            200,
                            ['Content-Type' => ['application/json']],
                            EntityJson::encode(
                                [
                                    'numFound' => 1,
                                    'docs' => [
                                        [
                                            'key' => 'test33kdjebwkcb',
                                            'title' => 'test',
                                            'firstPublishYear' => 2022,
                                            'authorName' => [
                                                'tester',
                                            ],
                                            'isbn' => [
                                                '0123456',
                                            ],
                                        ],
                                    ],
                                ]
                            ),
                        );
                    }

                    private function caseImport(RequestInterface $request): ResponseInterface
                    {
                        $data = (new StreamJson($request->getBody()))->decode();
                        if (array_key_exists('author', $data) && $data['author'] === 'exception') {
                            return new Response(500);
                        }

                        return new Response(
                            200,
                            ['Content-Type' => ['application/json']],
                            EntityJson::encode($data),
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
