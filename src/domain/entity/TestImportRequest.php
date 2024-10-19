<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\hydrate\EntityResponse;

/**
 * @implements RequestEntity<TestResponse>
 * @psalm-internal kuaukutsu\ps\onion\domain\service
 */
final readonly class TestImportRequest implements RequestEntity
{
    public function __construct(private TestImportData $data)
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
        return (string)$this->data;
    }

    #[Override]
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
