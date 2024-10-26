<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\domain\service\serialize\EntityResponse;

/**
 * @implements RequestEntity<Book>
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class BookRequest implements RequestEntity
{
    public function __construct(private string $uuid)
    {
    }

    #[Override]
    public function getMethod(): string
    {
        return 'GET';
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
    public function makeResponse(StreamDecode $stream): Book
    {
        return (new EntityResponse(Book::class))
            ->makeWithCamelCase(
                $stream->decode(),
                [
                    'uuid' => $this->uuid,
                    'title' => 'Name Default',
                    'author' => 'Author',
                ]
            );
    }
}
