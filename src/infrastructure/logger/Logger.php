<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger;

use Override;
use Stringable;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerPreset;

final readonly class Logger implements LoggerInterface
{
    /**
     * @psalm-internal kuaukutsu\ps\onion\application
     */
    public function __construct(private PsrLoggerInterface $logger)
    {
    }

    #[Override]
    public function preset(LoggerPreset $preset, string $category = 'application'): void
    {
        try {
            $this->logger->log(
                $preset->getLevel()->value,
                $preset->getMessage(),
                [
                    ...$preset->getContext(),
                    'category' => $category,
                ]
            );
        } catch (InvalidArgumentException $exception) {
            $this->error(
                $exception->getMessage(),
                [
                    'preset' => $preset,
                    'category' => $category,
                    'exception' => $exception,
                ]
            );
        }
    }

    #[Override]
    public function emergency(Stringable | string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    #[Override]
    public function alert(Stringable | string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    #[Override]
    public function critical(Stringable | string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    #[Override]
    public function error(Stringable | string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    #[Override]
    public function warning(Stringable | string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    #[Override]
    public function notice(Stringable | string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    #[Override]
    public function info(Stringable | string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    #[Override]
    public function debug(Stringable | string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    #[Override]
    public function log($level, Stringable | string $message, array $context = []): never
    {
        throw new NotImplementedException();
    }
}
