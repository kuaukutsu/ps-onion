<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\serialize;

use Override;
use kuaukutsu\ps\onion\domain\exception\UnsupportedException;
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

    /**
     * @throws UnsupportedException
     */
    #[Override]
    public function serialize(): never
    {
        throw new UnsupportedException();
    }

    /**
     * @throws UnsupportedException
     */
    #[Override]
    public function unserialize(string $data): never
    {
        throw new UnsupportedException();
    }

    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'object' => $this->object,
        ];
    }

    /**
     * @param array{
     *     "name": non-empty-string,
     *     "object": EntityDtoStub|null,
     * } $data
     */
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->object = $data['object'];
    }
}
