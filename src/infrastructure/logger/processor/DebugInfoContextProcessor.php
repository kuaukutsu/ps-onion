<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\logger\processor;

use Override;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use kuaukutsu\ps\onion\domain\interface\DebugInfoInterface;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\logger
 */
final class DebugInfoContextProcessor implements ProcessorInterface
{
    #[Override]
    public function __invoke(LogRecord $record): LogRecord
    {
        $rewrite = false;
        $context = $record->context;
        foreach ($context as $name => $value) {
            if ($value instanceof DebugInfoInterface) {
                $context[$name] = $value->__debugInfo();
                $rewrite = true;
            }
        }

        if ($rewrite) {
            return $record->with(context: $context);
        }

        return $record;
    }
}
