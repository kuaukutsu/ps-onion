<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\domain\exception\ClientRequestException;
use kuaukutsu\ps\onion\domain\exception\ConnectRequestException;
use kuaukutsu\ps\onion\domain\exception\ServerRequestException;
use kuaukutsu\ps\onion\domain\exception\UnexpectedRequestException;

interface ClientInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws ConnectRequestException
     * @throws ServerRequestException
     * @throws ClientRequestException
     * @throws UnexpectedRequestException
     */
    public function send(RequestInterface $request, RequestContext $context): ResponseInterface;
}
