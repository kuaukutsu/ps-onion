<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests;

use Override;
use DI\Definition\Helper\DefinitionHelper;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use GuzzleHttp\Psr7\HttpFactory;
use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\Validator\ValidatorInterface;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\LoggerDecorator;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\infrastructure\cache\FileCache;

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

    private static function setDefinition(string $id, DefinitionHelper $definition): void
    {
        if (self::$container === null) {
            self::$container = self::makeContainer();
        }

        self::$container->set($id, $definition);
    }

    /**
     * @template TClass
     * @param class-string<TClass> $id
     * @return TClass
     * @throws DependencyException
     * @throws NotFoundException
     */
    private static function make(string $id, array $parameters = [])
    {
        if (self::$container === null) {
            self::$container = self::makeContainer();
        }

        return self::$container->make($id, $parameters);
    }

    private static function makeContainer(): \DI\Container
    {
        return new \DI\Container(
            [
                ContainerInterface::class => factory(
                    static function (\DI\Container $container): ContainerInterface {
                        return new ContainerDecorator($container);
                    }
                ),
                RequestFactoryInterface::class => create(HttpFactory::class),
                StreamFactoryInterface::class => create(HttpFactory::class),
                ValidatorInterface::class => create(Validator::class),
                CacheInterface::class => create(FileCache::class),
                PsrLoggerInterface::class => create(NullLogger::class),
                LoggerInterface::class => factory(
                    fn(): LoggerInterface => new LoggerDecorator(
                        new class implements ApplicationInterface {
                            #[Override]
                            public function getName(): string
                            {
                                return 'test';
                            }

                            #[Override]
                            public function getVersion(): string
                            {
                                return '0.0.1';
                            }

                            #[Override]
                            public function getRuntime(): string
                            {
                                return '/tmp';
                            }
                        }
                    )
                ),
            ]
        );
    }
}
