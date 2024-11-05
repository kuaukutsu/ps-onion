<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\processor;

use Override;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\logger
 */
final readonly class ApplicationProcessor implements ProcessorInterface
{
    public function __construct(private ApplicationInterface $application)
    {
    }

    #[Override]
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['app'] = [
            'name' => $this->application->getName(),
            'version' => $this->application->getVersion(),
        ];

        return $record;
    }
}
