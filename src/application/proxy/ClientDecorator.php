<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\proxy;

use Override;
use InvalidArgumentException;
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

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class ClientDecorator implements ClientInterface
{
    private PsrClientInterface $client;

    /**
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        ContainerInterface $container,
    ) {
        $this->client = $container->make(PsrClientInterface::class);
    }

    #[Override]
    public function send(RequestInterface $request, RequestContext $context): ResponseInterface
    {
        try {
            $response = $this->client->sendRequest($request);
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
}
