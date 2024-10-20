<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\decorator;

use InvalidArgumentException;
use DI\Container;
use Override;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;

final readonly class ContainerDecorator implements ContainerInterface
{
    public function __construct(private Container $container)
    {
    }

    /**
     * @template TClass
     * @param class-string<TClass> $name
     * @param array $parameters
     * @return TClass
     * @throws InvalidArgumentException The name parameter must be of type string.
     * @throws ContainerExceptionInterface Error while resolving the entry.
     */
    #[Override]
    public function make(string $name, array $parameters = [])
    {
        /**
         * @var TClass
         */
        return $this->container->make($name, $parameters);
    }

    #[Override]
    public function get(string $id)
    {
        return $this->container->get($id);
    }

    #[Override]
    public function has(string $id): bool
    {
        return $this->container->has($id);
    }
}
