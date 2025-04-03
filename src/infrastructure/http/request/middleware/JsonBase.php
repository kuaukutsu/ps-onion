<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http\request\middleware;

use Override;
use Psr\Http\Message\RequestInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\infrastructure\http\RequestMiddleware;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class JsonBase implements RequestMiddleware
{
    #[Override]
    public function handle(RequestInterface $request, RequestContext $context, callable $next): RequestInterface
    {
        return $next(
            $request
                ->withAddedHeader('Accept', 'application/json')
                ->withAddedHeader('Cache-Control', 'no-cache')
                ->withAddedHeader('X-Request-Id', $context->getUuid()),
            $context,
        );
    }
}
