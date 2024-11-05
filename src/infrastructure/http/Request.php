<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Fig\Http\Message\RequestMethodInterface;

/**
 * @readonly
 */
interface Request extends RequestMethodInterface
{
    public function getMethod(): string;

    public function getUri(): string;

    public function getBody(): string;
}
