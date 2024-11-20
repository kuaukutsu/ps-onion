<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\serialize;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class EntityDtoStub implements EntityDto
{
    public function __construct(
        public string $name,
        public ?EntityDtoStub $object = null,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'object' => $this->object?->toArray(),
        ];
    }
}
