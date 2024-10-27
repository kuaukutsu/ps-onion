<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Override;
use Throwable;
use Psr\Http\Message\StreamInterface;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class StreamJson implements StreamDecode
{
    public function __construct(private StreamInterface $stream)
    {
    }

    #[Override]
    public function decode(): array
    {
        $body = trim((string)$this->stream);
        if ($body === '' || $body === '{}') {
            return [];
        }

        try {
            /**
             * @var array<string, scalar|array|null>
             */
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new StreamDecodeException($e->getMessage());
        }
    }
}
