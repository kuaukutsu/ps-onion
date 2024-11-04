<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

use Ramsey\Uuid\Uuid as UuidFactory;

final readonly class AuthorUuid
{
    /**
     * @var non-empty-string
     */
    public string $value;

    /**
     * @param non-empty-string|null $value
     */
    public function __construct(?string $value = null)
    {
        $this->value = $value ?? $this->generate();
    }

    /**
     * @return array{"uuid": non-empty-string}
     */
    public function toConditions(): array
    {
        return ['uuid' => $this->value];
    }

    /**
     * @return non-empty-string
     */
    private function generate(): string
    {
        /**
         * @var non-empty-string
         */
        return UuidFactory::uuid4()->toString();
    }
}
