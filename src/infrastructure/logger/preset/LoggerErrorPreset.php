<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\preset;

use Override;
use kuaukutsu\ps\onion\domain\interface\LoggerLevel;
use kuaukutsu\ps\onion\domain\interface\LoggerPreset;

final readonly class LoggerErrorPreset implements LoggerPreset
{
    public function __construct(
        private string $message,
        private array $context = [],
    ) {
    }

    #[Override]
    public function getLevel(): LoggerLevel
    {
        return LoggerLevel::ERROR;
    }

    #[Override]
    public function getMessage(): string
    {
        return $this->message;
    }

    #[Override]
    public function getContext(): array
    {
        return $this->context;
    }
}
