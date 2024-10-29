<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Override;
use RuntimeException;
use Psr\Http\Message\StreamInterface;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class StreamHtml implements StreamDecode
{
    /**
     * @phpstan-ignore constructor.unusedParameter
     */
    public function __construct(StreamInterface $stream)
    {
    }

    /**
     * @throws RuntimeException
     */
    #[Override]
    public function decode(): never
    {
        throw new RuntimeException('Not implemented.');
    }
}
