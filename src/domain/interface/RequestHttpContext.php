<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface RequestHttpContext extends RequestContext
{
    public function getTimeout(): float;
}
