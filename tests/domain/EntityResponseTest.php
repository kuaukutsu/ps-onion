<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\domain;

use Error;
use TypeError;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\domain\service\serialize\EntityResponse;

final class EntityResponseTest extends TestCase
{
    public function testSuccess(): void
    {
        $entityResponse = new EntityResponse(EntityStub::class);
        $entity = $entityResponse->makeWithCamelCase(
            [
                'name' => 'John',
                'object' => new EntityStub(name: 'Nested'),
            ]
        );

        self::assertInstanceOf(EntityStub::class, $entity);
        self::assertEquals('John', $entity->name);
        self::assertNotEmpty($entity->object);
        self::assertEquals('Nested', $entity->object->name);

        $entity = $entityResponse->makeWithCamelCase(
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

        $entityResponse = new EntityResponse(EntityStub::class);
        $entityResponse->makeWithCamelCase(
            [
            ]
        );
    }

    public function testFailureArgumentName(): void
    {
        $this->expectException(Error::class);

        $entityResponse = new EntityResponse(EntityStub::class);
        $entityResponse->makeWithCamelCase(
            [
                'name2' => 'John',
            ]
        );
    }
}
