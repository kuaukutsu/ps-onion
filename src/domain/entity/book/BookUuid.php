<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use Assert\Assert;
use LogicException;
use Ramsey\Uuid\Uuid as UuidFactory;

final readonly class BookUuid
{
    /**
     * @var non-empty-string
     */
    public string $value;

    /**
     * @param non-empty-string|null $value
     * @throws LogicException is not valid UUID
     */
    public function __construct(?string $value = null)
    {
        if ($value !== null) {
            Assert::that($value)->uuid();
        }

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
