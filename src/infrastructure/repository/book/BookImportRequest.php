<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use InvalidArgumentException;
use kuaukutsu\ps\onion\infrastructure\http\request\middleware\JsonBase;
use kuaukutsu\ps\onion\infrastructure\http\request\middleware\JsonBody;
use kuaukutsu\ps\onion\infrastructure\http\Container;
use kuaukutsu\ps\onion\infrastructure\http\RequestEntity;
use kuaukutsu\ps\onion\infrastructure\http\RequestMethod;
use kuaukutsu\ps\onion\infrastructure\http\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityJson;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<RecordData>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 */
final readonly class BookImportRequest implements RequestEntity
{
    private string $body;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(RecordData $data)
    {
        $this->body = EntityJson::encode($data->toArray());
    }

    #[Override]
    public function getMethod(): string
    {
        return RequestMethod::POST->value;
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://webhook.site/8cabc407-a3f0-41b3-8f53-b5f1edcff4f0';
    }

    #[Override]
    public function makeRequest(): array
    {
        return [
            new Container(class: JsonBase::class),
            new Container(
                class: JsonBody::class,
                parameters: [
                    'body' => $this->body,
                ]
            ),
        ];
    }

    #[Override]
    public function makeResponse(int $statusCode, StreamDecode $stream): RecordData
    {
        return EntityMapper::denormalize(RecordData::class, $stream->decode());
    }

    #[Override]
    public function __debugInfo(): array
    {
        return [
            'uri' => $this->getUri(),
            'method' => $this->getMethod(),
            'body' => $this->body,
        ];
    }
}
