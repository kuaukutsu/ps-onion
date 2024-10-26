<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http\request;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class HandlerContainer
{
    /**
     * @param class-string<HandlerInterface> $class
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        public string $class,
        public array $parameters = [],
    ) {
    }
}
