<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\serialize;

use TypeError;
use TypeLang\Mapper\Exception\MapperExceptionInterface;
use TypeLang\Mapper\Mapper;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure
 */
final readonly class EntityMapper
{
    /**
     * @template TResponse of EntityDto
     * @param class-string<TResponse> $entityClass
     * @param array<string, mixed> $data
     * @param array<string, mixed> $default
     * @return TResponse
     * @throws TypeError Unknown named parameter
     * @noinspection PhpDocSignatureInspection
     */
    public function denormalize(string $entityClass, array $data, array $default = []): EntityDto
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
            return (new Mapper())->denormalize($arguments, $entityClass);
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
