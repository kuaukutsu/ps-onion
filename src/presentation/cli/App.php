<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use Override;
use kuaukutsu\ps\onion\domain\interface\Application;

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\cli
 */
final readonly class App implements Application
{
    #[Override]
    public function getName(): string
    {
        return 'onion.cli';
    }

    #[Override]
    public function getVersion(): string
    {
        return '0.0.1';
    }

    #[Override]
    public function getRuntime(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'runtime';
    }
}
