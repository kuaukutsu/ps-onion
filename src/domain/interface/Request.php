<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use TypeError;
use InvalidArgumentException;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;

/**
 * @template TResponse of Response
 */
interface Request
{
    public function getMethod(): string;

    public function getUri(): string;

    /**
     * @throws InvalidArgumentException convert data to string
     */
    public function getBody(): string;

    /**
     * @return TResponse
     * @throws TypeError hydrate data
     * @throws StreamDecodeException decode string to array
     * @noinspection PhpDocSignatureInspection
     */
    public function makeResponse(StreamDecode $stream): Response;
}
