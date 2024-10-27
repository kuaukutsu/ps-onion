<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\http;

use kuaukutsu\ps\onion\domain\interface\Application;

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\http
 */
final readonly class App implements Application
{
    public function getName(): string
    {
        return 'onion.web';
    }

    public function getVersion(): string
    {
        return '0.0.1';
    }

    public function getRuntime(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'runtime';
    }
}
