<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\domain;

use Override;
use kuaukutsu\ps\onion\domain\interface\Entity;

final readonly class EntityStub implements Entity
{
    public function __construct(
        public string $name,
        public ?EntityStub $object = null,
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
