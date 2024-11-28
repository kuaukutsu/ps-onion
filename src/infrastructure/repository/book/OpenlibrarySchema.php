<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use kuaukutsu\ps\onion\domain\exception\UnsupportedException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class OpenlibrarySchema implements EntityDto
{
    /**
     * @param array<array{
     *     "key": non-empty-string,
     *     "title": non-empty-string,
     *     "firstPublishYear": int,
     *     "authorName": non-empty-string[],
     *     "isbn": non-empty-string[],
     * }> $docs
     */
    public function __construct(
        public int $numFound,
        public array $docs,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'numFound' => $this->numFound,
            'docs' => $this->docs,
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
     *     "numFound": int,
     *     "docs": array<array{
     *          "key": non-empty-string,
     *          "title": non-empty-string,
     *          "firstPublishYear": int,
     *          "authorName": non-empty-string[],
     *          "isbn": non-empty-string[],
     *      }>,
     * } $data
     */
    public function __unserialize(array $data): void
    {
        $this->numFound = $data['numFound'];
        $this->docs = $data['docs'];
    }
}
