<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\decorator;

use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\domain\exception\ClientRequestException;
use kuaukutsu\ps\onion\domain\exception\ConnectRequestException;
use kuaukutsu\ps\onion\domain\exception\ServerRequestException;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;

final readonly class ClientDecorator implements ClientInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    #[Override]
    public function send(RequestInterface $request, RequestContext $context): ResponseInterface
    {
        try {
            $response = $this->make()->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ConnectRequestException($request, $e);
        }

        $level = (int)floor($response->getStatusCode() / 100);
        if ($level === 4) {
            throw new ClientRequestException($request, $response);
        }

        if ($level === 5) {
            throw new ServerRequestException($request, $response);
        }

        return $response;
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function make(): PsrClientInterface
    {
        /**
         * @var PsrClientInterface
         */
        return $this->container->get(PsrClientInterface::class);
    }
}
