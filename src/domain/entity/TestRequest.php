<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\Request;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\hydrate\EntityResponse;

/**
 * @implements Request<TestResponse>
 * @psalm-internal kuaukutsu\ps\onion\domain\service
 */
final readonly class TestRequest implements Request
{
    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUri(): string
    {
        return 'https://webhook.site/5669bc32-92b7-4b31-9bc7-203b9d11438d';
    }

    /**
     * @throws NotImplementedException
     */
    public function getBody(): never
    {
        throw new NotImplementedException();
    }

    public function makeResponse(StreamDecode $stream): TestResponse
    {
        return (new EntityResponse(TestResponse::class))
            ->makeWithCamelCase(
                $stream->decode(),
                [
                    'name' => 'test default name',
                ]
            );
    }
}
