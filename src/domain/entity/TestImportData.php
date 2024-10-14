<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use InvalidArgumentException;
use kuaukutsu\ps\onion\domain\interface\EntityData;
use kuaukutsu\ps\onion\infrastructure\hydrate\Json;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class TestImportData implements EntityData
{
    public function __construct(
        private string $name,
        private int $time,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'time' => $this->time,
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function __toString(): string
    {
        return Json::encode($this->toArray());
    }
}
