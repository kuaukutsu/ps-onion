<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Stringable;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;

interface LoggerInterface extends PsrLoggerInterface
{
    public function preset(LoggerPreset $preset, string $category = 'application'): void;

    /**
     * @throws InvalidArgumentException
     * @throws NotImplementedException
     */
    public function log($level, Stringable | string $message, array $context = []): never;
}
