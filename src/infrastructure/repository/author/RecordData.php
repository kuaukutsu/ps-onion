<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\author;

use Override;
use kuaukutsu\ps\onion\domain\exception\UnsupportedException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

/**
 * Представление табличной записи.
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository\author
 */
final readonly class RecordData implements EntityDto
{
    /**
     * @param non-empty-string $uuid
     * @param non-empty-string $name
     * @param non-empty-string $createdAt
     * @param non-empty-string $updatedAt
     */
    public function __construct(
        public string $uuid,
        public string $name,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
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
        return $this->toArray();
    }

    /**
     * @param array{
     *     "uuid": non-empty-string,
     *     "name": non-empty-string,
     *     "created_at": non-empty-string,
     *     "updated_at": non-empty-string
     * } $data
     */
    public function __unserialize(array $data): void
    {
        $this->uuid = $data['uuid'];
        $this->name = $data['name'];
        $this->createdAt = $data['created_at'];
        $this->updatedAt = $data['updated_at'];
    }
}
