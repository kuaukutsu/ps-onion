<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure
 */
final readonly class Container
{
    /**
     * @param class-string<RequestMiddleware> $class
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        public string $class,
        public array $parameters = [],
    ) {
    }
}
