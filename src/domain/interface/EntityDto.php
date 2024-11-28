<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Serializable;

/**
 * @readonly
 */
interface EntityDto extends Serializable
{
    /**
     * @return array<string, scalar|array|null>
     */
    public function toArray(): array;
}
