<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\serialize;

use Error;
use TypeError;
use ReflectionClass;
use ReflectionException;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

/**
 * @template TResponse of EntityDto
 * @psalm-internal kuaukutsu\ps\onion\domain
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
     * @param array<string, mixed> $data
     * @param array<string, mixed> $default
     * @return TResponse
     * @throws Error Unknown named parameter
     * @throws TypeError
     * @noinspection PhpDocSignatureInspection
     * @psalm-internal kuaukutsu\ps\onion\domain\entity
     */
    public function makeWithCamelCase(array $data, array $default = []): EntityDto
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
