<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\domain\service\serialize\EntityJson;
use kuaukutsu\ps\onion\domain\service\serialize\EntityResponse;

/**
 * @implements RequestEntity<Book>
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class BookImportRequest implements RequestEntity
{
    public function __construct(private BookData $data)
    {
    }

    #[Override]
    public function getMethod(): string
    {
        return 'POST';
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://webhook.site/5669bc32-92b7-4b31-9bc7-203b9d11438d';
    }

    #[Override]
    public function getBody(): string
    {
        return EntityJson::encode($this->data->toArray());
    }

    #[Override]
    public function makeResponse(StreamDecode $stream): Book
    {
        return (new EntityResponse(Book::class))
            ->makeWithCamelCase(
                $stream->decode(),
                $this->data->toArray(),
            );
    }
}
