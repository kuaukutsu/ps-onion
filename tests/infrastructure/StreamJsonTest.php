<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure;

use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\StreamFactoryInterface;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\infrastructure\http\StreamJson;

final class StreamJsonTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testDecodeSuccess(): void
    {
        $streamJson = <<<JSON
{
  "name": "test stream json"
}
JSON;


        $streamCoder = new StreamJson(
            self::get(StreamFactoryInterface::class)
                ->createStream($streamJson),
        );

        $responseData = $streamCoder->decode();

        self::assertIsArray($responseData);
        self::assertArrayHasKey('name', $responseData);
        self::assertEquals('test stream json', $responseData['name']);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testDecodeFailure(): void
    {
        $streamCoder = new StreamJson(
            self::get(StreamFactoryInterface::class)
                ->createStream(''),
        );

        $this->expectException(StreamDecodeException::class);

        $streamCoder->decode();
    }
}
