<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\cache;

use Override;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\tests\infrastructure\http\RequestContextStub;

final class FileCacheTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    #[Override]
    public static function tearDownAfterClass(): void
    {
        $cache = self::get(CacheInterface::class);
        $cache->clear();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function testStringSuccess(): void
    {
        $cache = self::get(CacheInterface::class);

        $key = 'str';
        $value = 'string-test';

        self::assertTrue($cache->set($key, $value));
        self::assertTrue($cache->has($key));
        self::assertEquals($value, $cache->get($key));
        self::assertTrue($cache->delete($key));
        self::assertFalse($cache->has($key));
        self::assertEmpty($cache->get($key));
        self::assertEquals('test', $cache->get($key, 'test'));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function testArraySuccess(): void
    {
        $cache = self::get(CacheInterface::class);

        $key = 'arr';
        $value = ['a' => 1, 'b' => 2];

        self::assertTrue($cache->set($key, $value));
        self::assertTrue($cache->has($key));
        self::assertEquals($value, $cache->get($key));
        self::assertTrue($cache->delete($key));
        self::assertFalse($cache->has($key));
        self::assertEmpty($cache->get($key));
        self::assertEquals([1,2], $cache->get($key, [1,2]));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function testObjectSuccess(): void
    {
        $cache = self::get(CacheInterface::class);

        $key = 'object';
        $value = new RequestContextStub('test');

        self::assertTrue($cache->set($key, $value));
        self::assertTrue($cache->has($key));

        $object = $cache->get($key);
        self::assertInstanceOf(RequestContextStub::class, $object);
        self::assertEquals($value->getUuid(), $object->getUuid());

        self::assertTrue($cache->delete($key));
        self::assertFalse($cache->has($key));
        self::assertEmpty($cache->get($key));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function testClear(): void
    {
        $cache = self::get(CacheInterface::class);

        $value = 'test';

        self::assertTrue($cache->set('111', $value));
        self::assertTrue($cache->set('222', $value));
        self::assertTrue($cache->set('333', $value));
        self::assertTrue($cache->clear());

        self::assertFalse($cache->has('111'));
        self::assertFalse($cache->has('222'));
        self::assertFalse($cache->has('333'));

        self::assertTrue($cache->set('test', $value));
        self::assertEquals($value, $cache->get('test'));
    }
}
