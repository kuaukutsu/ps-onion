<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http\request;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http\request
 */
interface HandlerInterface
{
    /**
     * @param callable(RequestInterface $request, RequestContext $context): RequestInterface $next
     * @throws InvalidArgumentException from withAddedHeader(...)
     */
    public function handle(RequestInterface $request, RequestContext $context, callable $next): RequestInterface;
}
