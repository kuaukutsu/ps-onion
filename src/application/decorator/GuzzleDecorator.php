<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\decorator;

use Override;
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
use kuaukutsu\ps\onion\domain\interface\RequestHttpContext;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class GuzzleDecorator implements ClientInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    #[Override]
    public function send(RequestInterface $request, RequestContext $context): ResponseInterface
    {
        $options = [];
        if ($context instanceof RequestHttpContext) {
            $options[RequestOptions::TIMEOUT] = $context->getTimeout();
        }

        try {
            return $this->make()->send($request, $options);
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

    /**
     * @throws ContainerExceptionInterface
     */
    private function make(): Client
    {
        /**
         * @var Client
         */
        return $this->container->get(Client::class);
    }
}
