<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use TypeError;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\EntityDto as TResponse;

/**
 * @template TResponse of TResponse
 */
interface RequestEntity extends Request
{
    /**
     * @return TResponse
     * @throws TypeError hydrate data
     * @throws StreamDecodeException decode string to array
     * @noinspection PhpDocSignatureInspection
     */
    public function makeResponse(StreamDecode $stream): TResponse;
}
