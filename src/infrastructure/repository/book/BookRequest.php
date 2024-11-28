<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\infrastructure\http\RequestEntity;
use kuaukutsu\ps\onion\infrastructure\http\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<OpenlibraryBook>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 * @link https://openlibrary.org/dev/docs/api/search
 */
final readonly class BookRequest implements RequestEntity
{
    public function __construct(private BookIsbn $isbn)
    {
    }

    #[Override]
    public function getMethod(): string
    {
        return self::METHOD_GET;
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://openlibrary.org/search.json?'
            . http_build_query(
                [
                    ...$this->isbn->toConditions(),
                    'fields' => [
                        'key',
                        'title',
                        'first_publish_year',
                        'author_name',
                        'isbn',
                    ],
                ]
            );
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
    public function makeResponse(StreamDecode $stream): ?OpenlibraryBook
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
}
