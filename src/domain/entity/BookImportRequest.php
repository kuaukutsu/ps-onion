<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use InvalidArgumentException;
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
    private string $body;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(BookData $data)
    {
        $this->body = EntityJson::encode($data->toArray());
    }

    #[Override]
    public function getMethod(): string
    {
        return 'POST';
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://webhook.site/8cabc407-a3f0-41b3-8f53-b5f1edcff4f0';
    }

    #[Override]
    public function getBody(): string
    {
        return $this->body;
    }

    #[Override]
    public function makeResponse(StreamDecode $stream): Book
    {
        return (new EntityResponse(Book::class))
            ->makeWithCamelCase(
                $stream->decode(),
            );
    }
}
