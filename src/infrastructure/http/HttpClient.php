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
use kuaukutsu\ps\onion\infrastructure\http\request\HandlerContainer;
use kuaukutsu\ps\onion\infrastructure\http\request\JsonBase;
use kuaukutsu\ps\onion\infrastructure\http\request\JsonBody;

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
     * @return TResponse
     * @throws RequestException
     * @noinspection PhpDocSignatureInspection
     */
    public function send(RequestEntity $requestEntity, RequestContext $context): EntityDto
    {
        $requestHandlers = [
            new HandlerContainer(class: JsonBase::class),
        ];
        if ($this->isRequestHasBody($requestEntity)) {
            $requestHandlers[] = new HandlerContainer(
                class: JsonBody::class,
                parameters: [
                    'body' => $requestEntity->getBody(),
                ]
            );
        }

        $request = $this->requestBuilder->process($requestEntity, $context, $requestHandlers);
        $response = $this->client->send($request, $context);

        // response handler: middleware

        try {
            return $requestEntity->makeResponse(
                $this->makeStreamDecode($response),
            );
        } catch (Error | StreamDecodeException $e) {
            throw new UnexpectedRequestException($request, $e);
        }
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

    /**
     * @throws StreamDecodeException
     */
    private function makeStreamDecode(ResponseInterface $response): StreamDecode
    {
        $headerContentType = current($response->getHeader('Content-Type'));

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
