<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Override;
use Stringable;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;

interface LoggerInterface extends PsrLoggerInterface
{
    public function preset(LoggerPreset $preset, string $category = 'application'): void;

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function log($level, Stringable | string $message, array $context = []): never;
}
