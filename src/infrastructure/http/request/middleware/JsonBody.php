<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http\request\middleware;

use Override;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\infrastructure\http\RequestMiddleware;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final readonly class JsonBody implements RequestMiddleware
{
    public function __construct(
        public string $body,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    #[Override]
    public function handle(RequestInterface $request, RequestContext $context, callable $next): RequestInterface
    {
        return $next(
            $request
                ->withAddedHeader('Content-Type', 'application/json')
                ->withBody(
                    $this->streamFactory->createStream(
                        $this->body
                    )
                ),
            $context,
        );
    }
}
