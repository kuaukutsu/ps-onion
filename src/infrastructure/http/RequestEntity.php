<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use TypeError;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\DebugInfoInterface;
use kuaukutsu\ps\onion\domain\interface\EntityDto as TResponse;

/**
 * @template TResponse of TResponse
 */
interface RequestEntity extends DebugInfoInterface
{
    /**
     * @return non-empty-string
     */
    public function getMethod(): string;

    /**
     * @return non-empty-string
     */
    public function getUri(): string;

    /**
     * @return Container[]
     */
    public function makeRequest(): array;

    /**
     * @return TResponse|null
     * @throws TypeError hydrate data
     * @throws StreamDecodeException decode string to array
     * @noinspection PhpDocSignatureInspection
     */
    public function makeResponse(StreamDecode $stream): ?TResponse;
}
