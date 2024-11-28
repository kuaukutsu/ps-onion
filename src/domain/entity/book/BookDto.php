<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use Override;
use kuaukutsu\ps\onion\domain\exception\UnsupportedException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class BookDto implements EntityDto
{
    /**
     * @param non-empty-string $uuid
     * @param non-empty-string $title
     * @param non-empty-string $author
     * @param non-empty-string|null $description
     */
    public function __construct(
        public string $uuid,
        public string $title,
        public string $author,
        public ?string $description = null,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
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
     *     "title": non-empty-string,
     *     "author": non-empty-string,
     *     "description": non-empty-string|null
     * } $data
     */
    public function __unserialize(array $data): void
    {
        $this->uuid = $data['uuid'];
        $this->title = $data['title'];
        $this->author = $data['author'];
        $this->description = $data['description'];
    }
}
