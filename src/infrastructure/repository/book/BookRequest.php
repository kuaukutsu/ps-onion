<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<BookDto>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 */
final readonly class BookRequest implements RequestEntity
{
    public function __construct(private string $uuid)
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
        return 'https://webhook.site/' . $this->uuid;
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
    public function makeResponse(StreamDecode $stream): BookDto
    {
        return EntityMapper::denormalize(
            BookDto::class,
            $stream->decode(),
            [
                'uuid' => $this->uuid,
                'title' => 'Name Default',
                'author' => 'Author',
            ]
        );
    }
}
