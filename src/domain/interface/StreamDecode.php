<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Psr\Http\Message\StreamInterface;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;

interface StreamDecode
{
    public function __construct(StreamInterface $stream);

    /**
     * @return array<string, scalar|array|null>
     * @throws StreamDecodeException
     */
    public function decode(): array;
}
