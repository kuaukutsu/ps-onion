<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use LogicException;
use kuaukutsu\ps\onion\infrastructure\http\request\middleware\JsonBase;
use kuaukutsu\ps\onion\infrastructure\http\Container;
use kuaukutsu\ps\onion\infrastructure\http\RequestEntity;
use kuaukutsu\ps\onion\infrastructure\http\RequestMethod;
use kuaukutsu\ps\onion\infrastructure\http\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<OpenlibraryBook>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 * @link https://openlibrary.org/dev/docs/api/search
 */
final readonly class BookFindByPropertyRequest implements RequestEntity
{
    private string $query;

    /**
     * @param non-empty-string $title
     * @param non-empty-string|null $author
     * @throws LogicException
     */
    public function __construct(
        string $title,
        ?string $author = null,
    ) {
        $this->query = $this->prepareArgsToQueryString(
            [
                'title' => $title,
                'author' => $author,
            ]
        );
    }

    #[Override]
    public function getMethod(): string
    {
        return RequestMethod::GET->value;
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://openlibrary.org/search.json?' . $this->query;
    }

    #[Override]
    public function makeRequest(): array
    {
        return [
            new Container(class: JsonBase::class),
        ];
    }

    #[Override]
    public function makeResponse(int $statusCode, StreamDecode $stream): ?OpenlibraryBook
    {
        $openlibrarySchema = EntityMapper::denormalize(
            OpenlibrarySchema::class,
            $stream->decode(),
        );
        if ($openlibrarySchema->docs === []) {
            return null;
        }

        return EntityMapper::denormalize(
            OpenlibraryBook::class,
            current($openlibrarySchema->docs),
        );
    }

    #[Override]
    public function __debugInfo(): array
    {
        return [
            'uri' => $this->getUri(),
            'method' => $this->getMethod(),
            'body' => '',
        ];
    }

    /**
     * @param array<non-empty-string, non-empty-string|null> $args
     * @throws LogicException
     */
    private function prepareArgsToQueryString(array $args): string
    {
        $conditions = array_filter($args, static fn($value): bool => $value !== null);
        if ($conditions === []) {
            throw new LogicException('Property must not be empty.');
        }

        $conditions['fields'] = 'key,title,first_publish_year,author_name,isbn';

        return http_build_query($conditions);
    }
}
