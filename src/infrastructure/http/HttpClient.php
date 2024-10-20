<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Error;
use Override;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use kuaukutsu\ps\onion\domain\exception\RequestException;
use kuaukutsu\ps\onion\domain\exception\ResponseException;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\Request;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\domain\interface\RequestHttpContext;
use kuaukutsu\ps\onion\domain\interface\RequestHandler;
use kuaukutsu\ps\onion\domain\interface\Response;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;

final readonly class HttpClient implements RequestHandler
{
    public function __construct(
        private ContainerInterface $container,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    /**
     * @psalm-internal kuaukutsu\ps\onion\domain\service
     * @throws ContainerExceptionInterface
     * @throws RequestException
     * @throws ResponseException
     */
    #[Override]
    public function send(RequestEntity $request, RequestContext $context): Response
    {
        $client = $this->makeClient($context);

        try {
            $response = $client->sendRequest(
                $this->isRequestHasBody($request)
                    ? $this->makePostRequest($request, $context)
                    : $this->makeRequest($request, $context)
            );
        } catch (ClientExceptionInterface $e) {
            // Exception handler: middleware
            // prepare exception: GuzzleHttp\Exception
            // rule exception: retry, logger
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        } catch (InvalidArgumentException $e) {
            throw new RequestException($e->getMessage());
        }

        try {
            return $request->makeResponse(
                $this->makeStreamDecode($response),
            );
        } catch (Error | StreamDecodeException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function makeClient(RequestContext $context): ClientInterface
    {
        $config = [];
        if ($context instanceof RequestHttpContext) {
            $config['timeout'] = $context->getTimeout();
        }

        /**
         * @var ClientInterface
         */
        return $config === []
            ? $this->container->get(ClientInterface::class)
            : $this->container->make(ClientInterface::class, ['config' => $config]);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function makeRequest(Request $request, RequestContext $context): RequestInterface
    {
        return $this->requestFactory
            ->createRequest(
                $request->getMethod(),
                $request->getUri(),
            )
            ->withAddedHeader('Accept', 'application/json')
            ->withAddedHeader('X-Request-Id', $context->getUuid());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function makePostRequest(Request $request, RequestContext $context): RequestInterface
    {
        return $this->makeRequest($request, $context)
            ->withAddedHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream(
                    $request->getBody()
                )
            );
    }

    private function isRequestHasBody(Request $request): bool
    {
        return match ($request->getMethod()) {
            'GET',
            'HEAD',
            'OPTIONS' => false,
            default => true,
        };
    }

    private function makeStreamDecode(ResponseInterface $response): StreamDecode
    {
        $headers = $response->getHeader('Content-Type');

        return match (current($headers)) {
            'application/xml' => new StreamXml($response->getBody()),
            default => new StreamJson($response->getBody()),
        };
    }
}
