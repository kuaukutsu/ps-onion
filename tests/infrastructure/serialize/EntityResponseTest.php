<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\serialize;

use TypeError;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

final class EntityResponseTest extends TestCase
{
    public function testSuccess(): void
    {
        $entity = EntityMapper::denormalize(
            EntityDtoStub::class,
            [
                'name' => 'John',
                'object' => [
                    'name' => 'Nested',
                ],
            ]
        );

        self::assertInstanceOf(EntityDtoStub::class, $entity);
        self::assertEquals('John', $entity->name);
        self::assertNotEmpty($entity->object);
        self::assertEquals('Nested', $entity->object->name);

        $entity = EntityMapper::denormalize(
            EntityDtoStub::class,
            [
                'name' => 'Test',
            ]
        );

        self::assertEquals('Test', $entity->name);
        self::assertEmpty($entity->object);
    }

    public function testFailureArgumentRequired(): void
    {
        $this->expectException(TypeError::class);

        EntityMapper::denormalize(
            EntityDtoStub::class,
            [
            ]
        );
    }

    public function testFailureArgumentName(): void
    {
        $this->expectException(TypeError::class);

        EntityMapper::denormalize(
            EntityDtoStub::class,
            [
                'name2' => 'John',
            ]
        );
    }
}
