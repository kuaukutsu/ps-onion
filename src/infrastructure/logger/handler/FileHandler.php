<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\handler;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerFactoryHandler;

final readonly class FileHandler implements LoggerFactoryHandler
{
    public function __construct(
        private int $rotatingMaxFiles = 7,
    ) {
    }

    public function makeHandler(ApplicationInterface $application): HandlerInterface
    {
        return new RotatingFileHandler(
            $this->getLogPath($application),
            $this->rotatingMaxFiles,
            Level::Debug,
        );
    }

    private function getLogPath(ApplicationInterface $application): string
    {
        return $application->getRuntime() . DIRECTORY_SEPARATOR
            . 'logs' . DIRECTORY_SEPARATOR
            . $application->getName() . '.log';
    }
}
