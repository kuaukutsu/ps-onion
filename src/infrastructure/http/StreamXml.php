<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class StreamXml implements StreamDecode
{
    public function __construct(StreamInterface $stream)
    {
    }

    /**
     * @throws RuntimeException
     */
    public function decode(): never
    {
        throw new RuntimeException('Not implemented.');
    }
}
