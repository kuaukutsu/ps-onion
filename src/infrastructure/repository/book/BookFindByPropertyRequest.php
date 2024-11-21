<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\infrastructure\http\RequestEntity;
use kuaukutsu\ps\onion\infrastructure\http\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<BookDto>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 * @link https://openlibrary.org/dev/docs/api/search
 */
final readonly class BookFindByPropertyRequest implements RequestEntity
{
    private string $query;

    /**
     * @param non-empty-string|null $author
     * @param non-empty-string|null $title
     * @throws LogicException
     */
    public function __construct(
        ?string $author = null,
        ?string $title = null,
    ) {
        $this->query = $this->prepareArgsToQueryString(
            [
                'author' => $author,
                'title' => $title,
            ]
        );
    }

    #[Override]
    public function getMethod(): string
    {
        return self::METHOD_GET;
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://openlibrary.org/search.json?' . $this->query;
    }

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function getBody(): never
    {
        throw new NotImplementedException();
    }

    #[Override]
    public function makeResponse(StreamDecode $stream): ?BookDto
    {
        $openlibrarySchema = EntityMapper::denormalize(
            OpenlibrarySchema::class,
            $stream->decode(),
        );
        if ($openlibrarySchema->docs === []) {
            return null;
        }

        $openlibraryBook = EntityMapper::denormalize(
            OpenlibraryBook::class,
            current($openlibrarySchema->docs),
        );
        return new BookDto(
            uuid: $openlibraryBook->getUuid()->value,
            title: $openlibraryBook->title,
            author: $openlibraryBook->getAuthor(),
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

        $conditions['fields'] = [
            'title',
            'first_publish_year',
            'author_name',
            'isbn',
        ];

        return http_build_query($conditions);
    }
}
