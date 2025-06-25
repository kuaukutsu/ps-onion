<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Logger;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerFactoryHandler;
use kuaukutsu\ps\onion\infrastructure\logger\processor\ApplicationProcessor;
use kuaukutsu\ps\onion\infrastructure\logger\processor\DebugInfoContextProcessor;
use kuaukutsu\ps\onion\infrastructure\logger\processor\SystemEnvironmentProcessor;

final readonly class MonologFactory
{
    /**
     * @param array<LoggerFactoryHandler> $factoryHandler
     */
    public function __construct(
        private ApplicationInterface $application,
        private array $factoryHandler = [],
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function make(): LoggerInterface
    {
        return new Logger(
            $this->application->getName(),
            $this->makeHandlers(),
            [
                new WebProcessor(),
                new MemoryPeakUsageProcessor(),
                new ApplicationProcessor($this->application),
                new SystemEnvironmentProcessor(),
                new DebugInfoContextProcessor(),
            ],
        );
    }

    /**
     * @return list<HandlerInterface>
     */
    private function makeHandlers(): array
    {
        $handlers = [];
        foreach ($this->factoryHandler as $factoryHandler) {
            $handlers[] = $factoryHandler->makeHandler($this->application);
        }

        return $handlers;
    }
}
