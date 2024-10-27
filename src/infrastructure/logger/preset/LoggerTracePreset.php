<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\preset;

use kuaukutsu\ps\onion\domain\interface\LoggerLevel;
use kuaukutsu\ps\onion\domain\interface\LoggerPreset;

final readonly class LoggerTracePreset implements LoggerPreset
{
    public function __construct(
        private string $message,
        private array $context = [],
    ) {
    }

    public function getLevel(): LoggerLevel
    {
        return LoggerLevel::INFO;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
