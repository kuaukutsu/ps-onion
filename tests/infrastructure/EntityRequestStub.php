<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure;

use Override;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\domain\interface\Response;
use kuaukutsu\ps\onion\infrastructure\hydrate\EntityResponse;

/**
 * @implements RequestEntity<EntityStub>
 */
final readonly class EntityRequestStub implements RequestEntity
{
    #[Override]
    public function getMethod(): string
    {
        return 'stub';
    }

    #[Override]
    public function getUri(): string
    {
        return 'stub';
    }

    #[Override]
    public function getBody(): string
    {
        return 'stub';
    }

    #[Override]
    public function makeResponse(StreamDecode $stream): Response
    {
        return (new EntityResponse(EntityStub::class))
            ->makeWithCamelCase(
                $stream->decode()
            );
    }
}
