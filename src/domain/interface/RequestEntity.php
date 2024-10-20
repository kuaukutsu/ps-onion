<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Error;
use TypeError;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;

/**
 * @template TResponse of Response
 */
interface RequestEntity extends Request
{
    /**
     * @return TResponse
     * @throws Error Unknown named parameter
     * @throws TypeError hydrate data
     * @throws StreamDecodeException decode string to array
     * @noinspection PhpDocSignatureInspection
     */
    public function makeResponse(StreamDecode $stream): Response;
}
