<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use Override;
use Throwable;
use RuntimeException;
use Psr\Http\Message\RequestInterface;
use kuaukutsu\ps\onion\domain\interface\RequestException;

final class UnexpectedRequestException extends RuntimeException implements RequestException
{
    public function __construct(
        private readonly RequestInterface $request,
        Throwable $previous,
    ) {
        parent::__construct(
            sprintf(
                'Unexpected error: `%s %s` resulted in a `%s` message.',
                $request->getMethod(),
                $request->getUri(),
                $previous->getMessage(),
            ),
            (int)$previous->getCode(),
            $previous,
        );
    }

    #[Override]
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
