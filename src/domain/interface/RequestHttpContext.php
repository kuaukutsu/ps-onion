<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

/**
 * @readonly
 */
interface RequestHttpContext extends RequestContext
{
    public function getTimeout(): float;
}
