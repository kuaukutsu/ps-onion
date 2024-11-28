<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli\output;

interface ConsoleMessage
{
    /**
     * @return string[]
     */
    public function output(): array;
}
