<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

final readonly class AuthorMetadata
{
    /**
     * @var non-empty-string format php:Y-m-d H:i:s
     */
    public string $createdAt;

    /**
     * @var non-empty-string format php:Y-m-d H:i:s
     */
    public string $updatedAt;

    /**
     * @param non-empty-string|null $createdAt format php:Y-m-d H:i:s
     * @param non-empty-string|null $updatedAt format php:Y-m-d H:i:s
     */
    public function __construct(
        ?string $createdAt = null,
        ?string $updatedAt = null,
    ) {
        $this->createdAt = $this->castToDatetime($createdAt);
        $this->updatedAt = $this->castToDatetime($updatedAt);
    }

    /**
     * @param non-empty-string|null $date
     * @return non-empty-string
     */
    private function castToDatetime(?string $date): string
    {
        /**
         * @var non-empty-string
         */
        return $date ?? gmdate('Y-m-d H:i:s');
    }
}
