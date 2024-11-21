<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

final readonly class AuthorUuid
{
    /**
     * @param non-empty-string $value UUID
     */
    public function __construct(public string $value)
    {
    }

    /**
     * @return array{"uuid": non-empty-string}
     */
    public function toConditions(): array
    {
        return ['uuid' => $this->value];
    }
}
