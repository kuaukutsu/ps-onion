<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Error;
use Override;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\domain\exception\UnexpectedRequestException;
use kuaukutsu\ps\onion\domain\exception\StreamDecodeException;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\Request;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\domain\interface\Response;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
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
     * @psalm-internal kuaukutsu\ps\onion\domain\service
     * @throws RequestException
     */
    #[Override]
    public function send(RequestEntity $requestEntity, RequestContext $context): Response
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
