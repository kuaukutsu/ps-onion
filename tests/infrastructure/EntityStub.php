<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure;

use Override;
use kuaukutsu\ps\onion\domain\interface\Response;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class EntityStub implements EntityDto, Response
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
