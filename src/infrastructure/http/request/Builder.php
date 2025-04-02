<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http\request;

use Throwable;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\infrastructure\http\Container;
use kuaukutsu\ps\onion\infrastructure\http\RequestMiddleware;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final class Builder
{
    /**
     * @var Container[]
     */
    private array $containers = [];

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @param Container[] $containers
     */
    public function withMiddleware(array $containers): self
    {
        $self = clone $this;
        $self->containers = $containers;

        return $self;
    }

    public function build(string $method, string $uri, RequestContext $context): RequestInterface
    {
        return $this->handle(
            $this->requestFactory->createRequest($method, $uri),
            $context,
        );
    }

    private function handle(RequestInterface $request, RequestContext $context): RequestInterface
    {
        $container = array_shift($this->containers);
        if ($container !== null) {
            try {
                return $this
                    ->makeHandler($container)
                    ->handle(
                        $request,
                        $context,
                        $this->handle(...),
                    );
            } catch (Throwable) {
            }
        }

        return $request;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    private function makeHandler(Container $container): RequestMiddleware
    {
        /**
         * @var RequestMiddleware
         */
        return $this->container->make($container->class, $container->parameters);
    }
}
