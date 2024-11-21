<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use TypeError;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid as UuidFactory;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class OpenlibraryBook implements EntityDto
{
    /**
     * @param non-empty-string $title
     * @param non-empty-string[] $authorName
     * @param non-empty-string[] $isbn
     */
    public function __construct(
        public string $title,
        public int $firstPublishYear,
        public array $authorName,
        public array $isbn,
    ) {
    }

    public function getUuid(): UuidInterface
    {
        return UuidFactory::uuid8(
            hash('xxh3', implode(':', $this->isbn))
        );
    }

    /**
     * @return non-empty-string
     * @throws TypeError
     */
    public function getAuthor(): string
    {
        $name = current($this->authorName);
        if (is_string($name)) {
            return $name;
        }

        throw new TypeError("AuthorName must be string.");
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'uuid' => $this->title,
            'firstPublishYear' => $this->firstPublishYear,
            'authorName' => $this->authorName,
            'isbn' => $this->isbn,
        ];
    }
}
