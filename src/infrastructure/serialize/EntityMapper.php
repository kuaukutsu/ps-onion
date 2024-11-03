<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\serialize;

use TypeError;
use TypeLang\Mapper\Exception\MapperExceptionInterface;
use TypeLang\Mapper\Mapper;
use kuaukutsu\ps\onion\domain\interface\Entity;

/**
 * @template TResponse of Entity
 * @psalm-internal kuaukutsu\ps\onion\domain
 */
final readonly class EntityMapper
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
     * @throws TypeError Unknown named parameter
     * @noinspection PhpDocSignatureInspection
     */
    public function makeWithCamelCase(array $data, array $default = []): Entity
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
            return (new Mapper())->denormalize($arguments, $this->entityClass);
        } catch (MapperExceptionInterface $exception) {
            throw new TypeError($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @psalm-internal kuaukutsu\ps\onion\infrastructure\serialize
     */
    private function toCamelCase(string $variableName): string
    {
        $upper = static fn(
            array $matches
        ): string => /** @var array{"word": string|null} $matches */ strtoupper((string)$matches['word']);

        return (string)preg_replace_callback('~(_)(?<word>[a-z])~', $upper, $variableName);
    }
}
