<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Error;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\exception\UnexpectedRequestException;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\EntityDto;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\infrastructure\http\decode\StreamHtml;
use kuaukutsu\ps\onion\infrastructure\http\decode\StreamJson;
use kuaukutsu\ps\onion\infrastructure\http\decode\StreamXml;
use kuaukutsu\ps\onion\infrastructure\http\request\Builder;

final readonly class HttpClient
{
    public function __construct(
        private Builder $requestBuilder,
        private ClientInterface $client,
    ) {
    }

    /**
     * @template TResponse of EntityDto
     * @param RequestEntity<TResponse> $requestEntity
     * @return TResponse|null
     * @throws RequestException
     * @noinspection PhpDocSignatureInspection
     */
    public function send(RequestEntity $requestEntity, RequestContext $context): ?EntityDto
    {
        $request = $this->requestBuilder
            ->withMiddleware($requestEntity->makeRequest())
            ->build(
                $requestEntity->getMethod(),
                $requestEntity->getUri(),
                $context,
            );

        $response = $this->client->send($request, $context);
        // response handler: middleware, retry

        try {
            return $requestEntity->makeResponse(
                $this->makeStreamDecode($response),
            );
        } catch (Error | StreamDecodeException $e) {
            throw new UnexpectedRequestException($request, $e);
        }
    }

    /**
     * @throws StreamDecodeException
     */
    private function makeStreamDecode(ResponseInterface $response): StreamDecode
    {
        [$headerContentType] = explode(';', (string)current($response->getHeader('Content-Type')));
        return match ($headerContentType) {
            'application/xml' => new StreamXml($response->getBody()),
            'application/json' => new StreamJson($response->getBody()),
            'text/html' => new StreamHtml($response->getBody()),
            default => throw new StreamDecodeException(
                "Unsupported response content-type: $headerContentType"
            ),
        };
    }
}
