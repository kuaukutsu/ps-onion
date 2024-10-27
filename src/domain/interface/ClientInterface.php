<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @throws RequestException
     */
    public function send(RequestInterface $request, RequestContext $context): ResponseInterface;
}
