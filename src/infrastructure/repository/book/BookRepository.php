<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use LogicException;
use Ramsey\Uuid\UuidFactoryInterface;
use kuaukutsu\ps\onion\domain\entity\book\BookData;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\infrastructure\http\HttpClient;
use kuaukutsu\ps\onion\infrastructure\http\HttpContext;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerTracePreset;

final readonly class BookRepository
{
    public function __construct(
        private BookCache $cache,
        private HttpClient $client,
        private UuidFactoryInterface $uuidFactory,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws RequestException
     */
    public function get(string $uuid): BookDto
    {
        $cacheKey = BookCache::makeKey($uuid);
        $model = $this->cache->get($cacheKey)
            ?? $this->client->send(
                new BookRequest($uuid),
                new HttpContext(),
            );

        $this->logger->preset(
            new LoggerTracePreset('Book', ['book' => $model]),
            __METHOD__,
        );

        $this->cache->set($cacheKey, $model);
        return $model;
    }

    /**
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @throws RequestException
     * @throws LogicException
     */
    public function import(string $title, string $author): BookDto
    {
        $model = $this->findByTitle($title, $author);
        if ($model instanceof BookDto) {
            throw new LogicException("[$model->uuid] Book `$title` already exists.");
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
     * @param non-empty-string $author
     * @throws LogicException
     */
    private function findByTitle(string $title, string $author): ?BookDto
    {
        $cacheKey = BookCache::makeKey($author, $title);

        try {
            $model = $this->cache->get($cacheKey)
                ?? $this->client->send(
                    new BookFindByPropertyRequest(author: $author, title: $title),
                    new HttpContext(),
                );
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

            return null;
        }

        $this->cache->set($cacheKey, $model);
        return $model;
    }
}
