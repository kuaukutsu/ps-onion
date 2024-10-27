<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\preset;

use Throwable;
use kuaukutsu\ps\onion\domain\interface\LoggerLevel;
use kuaukutsu\ps\onion\domain\interface\LoggerPreset;

final readonly class LoggerExceptionPreset implements LoggerPreset
{
    private array $context;

    public function __construct(
        private Throwable $exception,
        array $context = [],
    ) {
        $this->context = [
            ...$context,
            'exception' => $exception,
        ];
    }

    public function getLevel(): LoggerLevel
    {
        return LoggerLevel::ERROR;
    }

    public function getMessage(): string
    {
        return $this->exception->getMessage();
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
