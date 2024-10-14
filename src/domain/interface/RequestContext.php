<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface RequestContext
{
    /**
     * @return non-empty-string UUID
     */
    public function getUuid(): string;
}
