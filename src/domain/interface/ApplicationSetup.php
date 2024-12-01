<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use RuntimeException;

interface ApplicationSetup
{
    /**
     * @throws RuntimeException
     */
    public function run(ApplicationInterface $application): void;
}
