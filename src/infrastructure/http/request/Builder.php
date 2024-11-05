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
use kuaukutsu\ps\onion\infrastructure\http\Request;

/**
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\http
 */
final class Builder
{
    /**
     * @var HandlerContainer[]
     */
    private array $handlers = [];

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @param HandlerContainer[] $handlers
     */
    public function process(Request $request, RequestContext $context, array $handlers): RequestInterface
    {
        $this->handlers = $handlers;

        return $this->handle(
            $this->requestFactory->createRequest(
                $request->getMethod(),
                $request->getUri(),
            ),
            $context,
        );
    }

    private function handle(RequestInterface $request, RequestContext $context): RequestInterface
    {
        $container = array_shift($this->handlers);
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
    private function makeHandler(HandlerContainer $container): HandlerInterface
    {
        /**
         * @var HandlerInterface
         */
        return $this->container->make($container->class, $container->parameters);
    }
}
