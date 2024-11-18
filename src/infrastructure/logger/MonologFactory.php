<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\infrastructure\logger\processor\ApplicationProcessor;
use kuaukutsu\ps\onion\infrastructure\logger\processor\DebugInfoContextProcessor;
use kuaukutsu\ps\onion\infrastructure\logger\processor\SystemEnvironmentProcessor;

final readonly class MonologFactory
{
    public function __construct(private ApplicationInterface $application)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function make(): LoggerInterface
    {
        return new Logger(
            $this->application->getName(),
            [
                new StreamHandler(
                    $this->getLogPath(),
                    Level::Debug,
                ),
            ],
            [
                new WebProcessor(),
                new MemoryPeakUsageProcessor(),
                new ApplicationProcessor($this->application),
                new SystemEnvironmentProcessor(),
                new DebugInfoContextProcessor(),
            ],
        );
    }

    private function getLogPath(): string
    {
        return $this->application->getRuntime()
            . DIRECTORY_SEPARATOR
            . 'logs'
            . DIRECTORY_SEPARATOR
            . $this->application->getName() . '.log';
    }
}
