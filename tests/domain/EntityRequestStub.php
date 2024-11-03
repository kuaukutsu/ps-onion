<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\domain;

use Override;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\Entity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

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
    public function makeResponse(StreamDecode $stream): Entity
    {
        return (new EntityMapper(EntityStub::class))
            ->makeWithCamelCase(
                $stream->decode()
            );
    }
}
