<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\hydrate;

use Error;
use ReflectionClass;
use ReflectionException;
use TypeError;
use kuaukutsu\ps\onion\domain\interface\Response;

/**
 * @template TResponse of Response
 */
final readonly class EntityResponse
{
    /**
     * @param class-string<TResponse> $entityClass
     */
    public function __construct(private string $entityClass)
    {
    }

    /**
     * @param array<string, scalar|array|null> $data
     * @param array<string, scalar|array|null> $default
     * @return TResponse
     * @throws Error Unknown named parameter
     * @throws TypeError
     * @noinspection PhpDocSignatureInspection
     * @psalm-internal kuaukutsu\ps\onion\domain\entity
     */
    public function makeWithCamelCase(array $data, array $default = []): Response
    {
        if ($default !== []) {
            $data = [...$default, ...$data];
        }

        $arguments = [];
        foreach ($data as $key => $value) {
            $arguments[$this->toCamelCase($key)] = $value;
        }

        try {
            /**
             * @var TResponse
             */
            return (new ReflectionClass($this->entityClass))->newInstanceArgs($arguments);
        } catch (ReflectionException $exception) {
            throw new TypeError(message: $exception->getMessage(), previous: $exception);
        }
    }

    private function toCamelCase(string $variableName): string
    {
        $upper = static fn(
            array $matches
        ): string => /** @var array{"word": string|null} $matches */ strtoupper((string)$matches['word']);

        return (string)preg_replace_callback('~(_)(?<word>[a-z])~', $upper, $variableName);
    }
}
