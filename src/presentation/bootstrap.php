<?php

declare(strict_types=1);

use DI\Container;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;
use kuaukutsu\ps\onion\application\decorator\CacheDecorator;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\GuzzleDecorator;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

return [
    ContainerInterface::class => factory(
        static fn (Container $container): ContainerInterface => new ContainerDecorator($container)
    ),
    RequestFactoryInterface::class => create(HttpFactory::class),
    StreamFactoryInterface::class => create(HttpFactory::class),
    UuidFactoryInterface::class => create(UuidFactory::class),
    ValidatorInterface::class => create(Validator::class),
    CacheInterface::class => create(CacheDecorator::class),
    ClientInterface::class => autowire(GuzzleDecorator::class),
];
