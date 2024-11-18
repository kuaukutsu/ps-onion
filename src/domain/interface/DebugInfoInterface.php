<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface DebugInfoInterface
{
    public function __debugInfo(): array;
}
