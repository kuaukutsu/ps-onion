<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

final readonly class BookIsbn
{
    /**
     * @param non-empty-string $isbn
     */
    public function __construct(private string $isbn)
    {
    }

    /**
     * @return non-empty-string
     */
    public function getValue(): string
    {
        return $this->isbn;
    }

    /**
     * @return array{"isbn": non-empty-string}
     */
    public function toConditions(): array
    {
        return ['isbn' => $this->isbn];
    }
}
