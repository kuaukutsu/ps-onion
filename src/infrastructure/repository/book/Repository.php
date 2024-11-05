<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use kuaukutsu\ps\onion\domain\entity\book\BookUuid;
use Override;
use LogicException;
use Ramsey\Uuid\UuidFactoryInterface;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\exception\ClientRequestException;
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
        private UuidFactoryInterface $uuidFactory,
        private LoggerInterface $logger,
    ) {
    }

    #[Override]
    public function get(BookUuid $uuid): BookDto
    {
        $cacheKey = Cache::makeKey($uuid->value);

        try {
            $model = $this->cache->get($cacheKey)
                ?? $this->client->send(
                    new BookRequest($uuid),
                    new HttpContext(),
                );
        } catch (RequestException $exception) {
            $this->logger->preset(
                new LoggerExceptionPreset($exception, $uuid->toConditions()),
                __METHOD__,
            );

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }

        /** @psalm-check-type-exact $model = BookDto */

        $this->cache->set($cacheKey, $model);
        return $model;
    }

    #[Override]
    public function import(string $title, string $author): BookDto
    {
        $model = $this->findByTitle($title, $author);
        if ($model instanceof BookDto) {
            throw new LogicException("[$model->uuid] Book `$title` already exists.");
        }

        try {
            return $this->client->send(
                new BookImportRequest(
                    new BookDto(
                        uuid: $this->uuidFactory->uuid4()->toString(),
                        title: $title,
                        description: $title,
                        author: $author,
                    )
                ),
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

            throw new InfrastructureException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @param non-empty-string $title
     * @param non-empty-string $author
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

        $this->cache->set($cacheKey, $model);
        return $model;
    }
}
