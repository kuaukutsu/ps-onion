<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\processor;

use Override;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\logger
 */
final class SystemEnvironmentProcessor implements ProcessorInterface
{
    #[Override]
    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            extra: [
                'env' => [
                    'docker' => getenv('HOSTNAME'),
                    'php' => getenv('PHP_VERSION'),
                ],
            ]
        );
    }
}
