<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\repository;

use kuaukutsu\ps\onion\domain\interface\EntityDto;
use kuaukutsu\ps\onion\infrastructure\http\RequestEntity;
use kuaukutsu\ps\onion\infrastructure\http\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;
use Override;

/**
 * @implements RequestEntity<EntityDtoStub>
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
    public function makeResponse(StreamDecode $stream): EntityDto
    {
        return EntityMapper::denormalize(EntityDtoStub::class, $stream->decode());
    }

    #[Override]
    public function __debugInfo(): array
    {
        return [
            'uri' => $this->getUri(),
            'method' => $this->getMethod(),
            'body' => $this->getBody(),
        ];
    }
}
