<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\Book;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\entity\book\BookMapper;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
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
    ) {
    }

    #[Override]
    public function get(BookIsbn $isbn): Book
    {
        $cacheKey = Cache::makeKey($isbn->getValue());

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

        if ($model === null) {
            throw new NotFoundException("[{$isbn->getValue()}] Book not found.");
        }

        /** @psalm-check-type-exact $model = BookDto */

        $this->cache->set($cacheKey, $model);
        return BookMapper::toModel($model);
    }

    #[Override]
    public function import(Book $book): Book
    {
        $request = new BookImportRequest(
            BookMapper::toDto($book)
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

    #[Override]
    public function find(Book $book): ?Book
    {
        $dto = $this->findByTitle($book->title->name, $book->author->name);
        if ($dto === null) {
            return null;
        }

        return BookMapper::toModel($dto);
    }

    /**
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws LogicException
     * @throws LogicException
     * @throws InfrastructureException
     */
    private function findByTitle(string $title, string $author): ?BookDto
    {
        $cacheKey = Cache::makeKey($author, $title);

        try {
            $model = $this->cache->get($cacheKey)
                ?? $this->client->send(
                    new BookFindByPropertyRequest(author: $author, title: $title),
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

        if ($model === null) {
            return null;
        }

        $this->cache->set($cacheKey, $model);
        return $model;
    }
}
