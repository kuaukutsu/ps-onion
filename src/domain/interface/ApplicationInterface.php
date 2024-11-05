<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface ApplicationInterface
{
    /**
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * @return non-empty-string
     */
    public function getVersion(): string;

    /**
     * @return non-empty-string
     */
    public function getRuntime(): string;
}
