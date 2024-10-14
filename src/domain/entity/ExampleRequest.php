<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\Request;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\hydrate\EntityResponse;

/**
 * @implements Request<ExampleResponse>
 * @psalm-internal kuaukutsu\ps\onion\domain\service
 */
final readonly class ExampleRequest implements Request
{
    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUri(): string
    {
        return 'http://localhost:8080/example';
    }

    /**
     * @throws NotImplementedException
     */
    public function getBody(): never
    {
        throw new NotImplementedException();
    }

    public function makeResponse(StreamDecode $stream): ExampleResponse
    {
        return (new EntityResponse(ExampleResponse::class))
            ->makeWithCamelCase($stream->decode());
    }
}
