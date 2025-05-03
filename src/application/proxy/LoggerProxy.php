<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\proxy;

use Override;
use Stringable;
use InvalidArgumentException;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerPreset;
use kuaukutsu\ps\onion\infrastructure\logger\MonologFactory;
use kuaukutsu\ps\onion\infrastructure\logger\Logger;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class LoggerProxy implements LoggerInterface
{
    private LoggerInterface $logger;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(ApplicationInterface $application)
    {
        $factory = new MonologFactory($application);
        $this->logger = new Logger(
            $factory->make()
        );
    }

    #[Override]
    public function preset(LoggerPreset $preset, string $category = 'application'): void
    {
        $this->logger->preset($preset, $category);
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
