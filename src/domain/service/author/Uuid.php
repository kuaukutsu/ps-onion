<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\author;

use Ramsey\Uuid\Uuid as UuidFactory;

final readonly class Uuid
{
    /**
     * @var non-empty-string
     */
    private string $uuid;

    /**
     * @param non-empty-string|null $uuid
     */
    public function __construct(?string $uuid = null)
    {
        $this->uuid = $uuid ?? $this->generate();
    }

    /**
     * @return non-empty-string
     */
    public function toValue(): string
    {
        return $this->uuid;
    }

    /**
     * @return array{"uuid": non-empty-string}
     */
    public function toConditions(): array
    {
        return ['uuid' => $this->toValue()];
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
