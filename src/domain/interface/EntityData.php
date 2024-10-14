<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Stringable;

/**
 * @readonly
 */
interface EntityData extends EntityDto, Stringable
{
}
