<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Fig\Http\Message\RequestMethodInterface;
use kuaukutsu\ps\onion\domain\interface\DebugInfoInterface;

/**
 * @readonly
 */
interface Request extends RequestMethodInterface, DebugInfoInterface
{
    public function getMethod(): string;

    public function getUri(): string;

    public function getBody(): string;
}
