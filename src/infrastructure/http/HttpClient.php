<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Error;
use Override;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use kuaukutsu\ps\onion\domain\exception\UnexpectedRequestException;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\Request;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\domain\interface\RequestHandler;
use kuaukutsu\ps\onion\domain\interface\Response;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;

final readonly class HttpClient implements RequestHandler
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    /**
     * @psalm-internal kuaukutsu\ps\onion\domain\service
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    #[Override]
    public function send(RequestEntity $requestEntity, RequestContext $context): Response
    {
        $request = $this->isRequestHasBody($requestEntity)
            ? $this->makePostRequest($requestEntity, $context)
            : $this->makeRequest($requestEntity, $context);

        $response = $this->client->send($request, $context);

        // Exception handler: middleware
        // prepare exception: GuzzleHttp\Exception
        // rule exception: retry, logger

        try {
            return $requestEntity->makeResponse(
                $this->makeStreamDecode($response),
            );
        } catch (Error | StreamDecodeException $e) {
            throw new UnexpectedRequestException($request, $e);
        }
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
