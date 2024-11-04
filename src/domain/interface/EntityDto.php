<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

/**
 * @readonly
 */
interface EntityDto
{
    /**
     * @return array<string, scalar|array|null>
     */
    public function toArray(): array;
}
