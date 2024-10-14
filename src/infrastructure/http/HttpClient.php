<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use InvalidArgumentException;
use TypeError;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use kuaukutsu\ps\onion\domain\exception\RequestException;
use kuaukutsu\ps\onion\domain\exception\ResponseException;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\Request;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
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
     */
    public function send(Request $request, RequestContext $context): Response
    {
        try {
            $response = $this->client->sendRequest(
                $this->isRequestHasBody($request)
                    ? $this->makePostRequest($request, $context)
                    : $this->makeRequest($request, $context)
            );
        } catch (InvalidArgumentException | ClientExceptionInterface $e) {
            // Exception handler: middleware
            throw new RequestException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            return $request->makeResponse(
                $this->makeStreamDecode($response),
            );
        } catch (TypeError | StreamDecodeException $e) {
            throw new ResponseException($e->getMessage(), $e->getCode(), $e);
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
