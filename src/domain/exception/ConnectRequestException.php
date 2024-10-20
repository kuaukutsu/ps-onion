<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use Override;
use RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Client\ClientExceptionInterface;
use kuaukutsu\ps\onion\domain\interface\RequestException;

final class ConnectRequestException extends RuntimeException implements RequestException
{
    public function __construct(
        private readonly RequestInterface $request,
        ?ClientExceptionInterface $previous = null,
    ) {
        if ($previous instanceof ClientExceptionInterface) {
            parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
        } else {
            parent::__construct(
                sprintf(
                    'Connect error: `%s %s`',
                    $request->getMethod(),
                    $request->getUri()
                )
            );
        }
    }

    #[Override]
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
