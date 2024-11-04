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
    private function __construct()
    {
    }

    /**
     * @template TResponse of EntityDto
     * @param class-string<TResponse> $entityClass
     * @param array<string, mixed> $data
     * @param array<string, mixed> $default
     * @return TResponse
     * @throws TypeError Unknown named parameter
     * @noinspection PhpDocSignatureInspection
     */
    public static function denormalize(string $entityClass, array $data, array $default = []): EntityDto
    {
        if ($default !== []) {
            $data = [...$default, ...$data];
        }

        $mapper = new Mapper();

        try {
            /**
             * @var TResponse
             */
            return $mapper->denormalize(self::prepareData($data), $entityClass);
        } catch (MapperExceptionInterface $exception) {
            throw new TypeError($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function prepareData(array $data): array
    {
        $arguments = [];
        foreach ($data as $key => $value) {
            $arguments[self::toCamelCase($key)] = $value;
        }

        return $arguments;
    }

    private static function toCamelCase(string $variableName): string
    {
        $upper = static fn(
            array $matches
        ): string => /** @var array{"word": string|null} $matches */ strtoupper((string)$matches['word']);

        return (string)preg_replace_callback('~(_)(?<word>[a-z])~', $upper, $variableName);
    }
}
