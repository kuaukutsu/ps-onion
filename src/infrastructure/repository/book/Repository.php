<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use TypeError;
use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\ClientRequestException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\infrastructure\http\HttpClient;
use kuaukutsu\ps\onion\infrastructure\http\HttpContext;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

final readonly class Repository implements BookRepository
{
    public function __construct(
        private Cache $cache,
        private HttpClient $client,
        private LoggerInterface $logger,
        private Mapper $mapper,
    ) {
    }

    #[Override]
    public function get(BookIsbn $isbn): Book
    {
        $cacheKey = Cache::makeKey('isbn', $isbn->getValue());

        try {
            $model = $this->cache->get($cacheKey)
                ?? $this->client->send(
                    new BookRequest($isbn),
                    new HttpContext(timeout: 10.),
                );
        } catch (RequestException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception, $isbn->toConditions()),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        if ($model instanceof OpenlibraryBook) {
            $this->cache->set($cacheKey, $model);
            return $this->mapper->fromOpenlibraryBook($model);
        }

        if ($model instanceof BookDto) {
            $this->cache->set($cacheKey, $model);
            return $this->mapper->fromDto($model);
        }

        throw new NotFoundException("[{$isbn->getValue()}] Book ISBN not found.");
    }

    #[Override]
    public function find(BookTitle $title, ?BookAuthor $author = null): ?Book
    {
        return $this->findByTitle($title->name, $author?->name);
    }

    #[Override]
    public function import(Book $book): Book
    {
        $request = new BookImportRequest(
            $this->mapper->toDto($book),
        );

        try {
            $this->client->send($request, new HttpContext());
        } catch (RequestException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception, ['request' => $request]),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        return $book;
    }

    /**
     * @param non-empty-string $title
     * @param non-empty-string|null $author
     * @throws TypeError
     * @throws LogicException
     * @throws InfrastructureException
     */
    private function findByTitle(string $title, ?string $author): ?Book
    {
        $cacheKey = $author === null
            ? Cache::makeKey('title', $title)
            : Cache::makeKey('title', $title, $author);

        try {
            $model = $this->cache->get($cacheKey)
                ?? $this->client->send(
                    new BookFindByPropertyRequest(title: $title, author: $author),
                    new HttpContext(),
                );
        } catch (ClientRequestException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset(
                    $exception,
                    [
                        'title' => $title,
                        'author' => $author,
                    ]
                ),
                __METHOD__,
            );

            return null;
        } catch (RequestException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset(
                    $exception,
                    [
                        'title' => $title,
                        'author' => $author,
                    ]
                ),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        if ($model instanceof OpenlibraryBook) {
            $this->cache->set($cacheKey, $model);
            return $this->mapper->fromOpenlibraryBook($model);
        }

        if ($model instanceof BookDto) {
            $this->cache->set($cacheKey, $model);
            return $this->mapper->fromDto($model);
        }

        return null;
    }
}
