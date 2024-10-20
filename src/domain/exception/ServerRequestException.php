<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use Override;
use RuntimeException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use kuaukutsu\ps\onion\domain\interface\RequestException;

final class ServerRequestException extends RuntimeException implements RequestException
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResponseInterface $response,
        ?ClientExceptionInterface $previous = null,
    ) {
        if ($previous instanceof ClientExceptionInterface) {
            parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
        } else {
            parent::__construct(
                sprintf(
                    'Client error: `%s %s` resulted in a `%s %s` response',
                    $request->getMethod(),
                    $request->getUri(),
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
        }
    }

    #[Override]
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
