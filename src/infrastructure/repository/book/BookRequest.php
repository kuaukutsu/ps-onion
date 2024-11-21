<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\entity\book\BookIsbn;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\infrastructure\http\RequestEntity;
use kuaukutsu\ps\onion\infrastructure\http\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<BookDto>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
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
            . http_build_query($this->isbn->toConditions());
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
            uuid: $openlibraryBook->getUuid()->toString(),
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
}
