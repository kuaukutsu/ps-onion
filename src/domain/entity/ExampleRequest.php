<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\hydrate\EntityResponse;

/**
 * @implements RequestEntity<ExampleResponse>
 * @psalm-internal kuaukutsu\ps\onion\domain\service
 */
final readonly class ExampleRequest implements RequestEntity
{
    #[Override]
    public function getMethod(): string
    {
        return 'GET';
    }

    #[Override]
    public function getUri(): string
    {
        return 'http://localhost:8080/example';
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
    public function makeResponse(StreamDecode $stream): ExampleResponse
    {
        return (new EntityResponse(ExampleResponse::class))
            ->makeWithCamelCase($stream->decode());
    }
}
