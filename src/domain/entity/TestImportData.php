<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class TestImportData implements EntityDto
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
}
