<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\book;

use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Ramsey\Uuid\UuidFactoryInterface;
use kuaukutsu\ps\onion\domain\entity\Book;
use kuaukutsu\ps\onion\domain\entity\BookData;
use kuaukutsu\ps\onion\domain\entity\BookFindByPropertyRequest;
use kuaukutsu\ps\onion\domain\entity\BookImportRequest;
use kuaukutsu\ps\onion\domain\entity\BookRequest;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\infrastructure\http\HttpClient;
use kuaukutsu\ps\onion\infrastructure\http\HttpContext;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class Repository
{
    public function __construct(
        private BookCache $cache,
        private HttpClient $client,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    /**
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function get(string $uuid): Book
    {
        $model = $this->cache->find($uuid)
            ?? $this->client->send(
                new BookRequest($uuid),
                new HttpContext(),
            );

        $this->cache->set($model);
        return $model;
    }

    /**
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function import(string $title, string $author): Book
    {
        $model = $this->findByTitle($title);
        if ($model instanceof Book) {
            throw new LogicException("Book `$title` already exists.");
        }

        return $this->client->send(
            new BookImportRequest(
                new BookData(
                    uuid: $this->uuidFactory->uuid4()->toString(),
                    title: $title,
                    author: $author,
                )
            ),
            new HttpContext(),
        );
    }

    /**
     * @param non-empty-string $title
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws LogicException
     * @psalm-internal kuaukutsu\ps\onion\domain\service\book
     */
    private function findByTitle(string $title): ?Book
    {
        try {
            return $this->client->send(
                new BookFindByPropertyRequest(title: $title),
                new HttpContext(),
            );
        } catch (RequestException) {
            return null;
        }
    }
}
