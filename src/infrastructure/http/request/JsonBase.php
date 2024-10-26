<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http\request;

use Psr\Http\Message\RequestInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class JsonBase implements HandlerInterface
{
    public function handle(RequestInterface $request, RequestContext $context, callable $next): RequestInterface
    {
        return $next(
            $request
                ->withAddedHeader('Accept', 'application/json')
                ->withAddedHeader('X-Request-Id', $context->getUuid()),
            $context,
        );
    }
}
