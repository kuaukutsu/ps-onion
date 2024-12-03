<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use TypeError;
use kuaukutsu\ps\onion\domain\service\BookUuidGenerator;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository\book
 */
final readonly class OpenlibraryMapper
{
    public function __construct(private BookUuidGenerator $uuidGenerator)
    {
    }

    /**
     * @throws TypeError если получены не корректные данные
     */
    public function fromOpenlibraryBook(OpenlibraryBook $openlibraryBook): RecordData
    {
        return new RecordData(
            uuid: $this->uuidGenerator->generateByKey($openlibraryBook->key)->value,
            title: $openlibraryBook->title,
            author: $this->prepareOpenlibraryBookAuthor($openlibraryBook)
        );
    }

    /**
     * @return non-empty-string
     * @throws TypeError
     */
    private function prepareOpenlibraryBookAuthor(OpenlibraryBook $openlibraryBook): string
    {
        $name = current($openlibraryBook->authorName);
        if (is_string($name)) {
            return $name;
        }

        throw new TypeError("AuthorName must be string.");
    }
}
