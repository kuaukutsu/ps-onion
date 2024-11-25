<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\decorator;

use Override;
use InvalidArgumentException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\domain\exception\ClientRequestException;
use kuaukutsu\ps\onion\domain\exception\ConnectRequestException;
use kuaukutsu\ps\onion\domain\exception\ServerRequestException;
use kuaukutsu\ps\onion\domain\exception\UnexpectedRequestException;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class GuzzleDecorator implements ClientInterface
{
    private Client $client;

    /**
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        ContainerInterface $container,
    ) {
        $this->client = $container->make(Client::class);
    }

    #[Override]
    public function send(RequestInterface $request, RequestContext $context): ResponseInterface
    {
        $options = [
            RequestOptions::TIMEOUT => $context->getTimeout(),
        ];

        try {
            return $this->client->send($request, $options);
        } catch (ClientException $e) {
            throw new ClientRequestException($request, $e->getResponse(), $e);
        } catch (ServerException $e) {
            throw new ServerRequestException($request, $e->getResponse(), $e);
        } catch (ConnectException $e) {
            throw new ConnectRequestException($request, $e);
        } catch (GuzzleException $e) {
            throw new UnexpectedRequestException($request, $e);
        }
    }
}
