<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests;

use DI\Definition\Helper\FactoryDefinitionHelper;
use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Psr7\HttpFactory;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function DI\create;
use function DI\factory;

trait Container
{
    private static ?\DI\Container $container = null;

    /**
     * @template TClass
     * @param class-string<TClass> $id
     * @return TClass
     * @throws DependencyException
     * @throws NotFoundException
     */
    private static function get(string $id)
    {
        if (self::$container === null) {
            self::$container = self::makeContainer();
        }

        return self::$container->get($id);
    }

    private static function setDefinition(string $id, FactoryDefinitionHelper $definition): void
    {
        if (self::$container === null) {
            self::$container = self::makeContainer();
        }

        self::$container->set($id, $definition);
    }

    private static function makeContainer(): \DI\Container
    {
        return new \DI\Container(
            [
                RequestFactoryInterface::class => create(HttpFactory::class),
                StreamFactoryInterface::class => create(HttpFactory::class),
                ContainerInterface::class => factory(
                    static function (\DI\Container $container): ContainerInterface {
                        return new ContainerDecorator($container);
                    }
                ),
            ]
        );
    }
}
