<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @readonly
 */
interface RequestException extends ClientExceptionInterface
{
    public function getRequest(): RequestInterface;
}
