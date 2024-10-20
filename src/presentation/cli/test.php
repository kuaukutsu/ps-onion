<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use kuaukutsu\ps\onion\application\Test;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\entity\TestResponse;
use kuaukutsu\ps\onion\domain\exception\RequestException;
use kuaukutsu\ps\onion\domain\exception\ResponseException;
use kuaukutsu\ps\onion\infrastructure\container\ContainerDecorator;

use function DI\create;
use function DI\factory;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

$container = new Container(
    [
        RequestFactoryInterface::class => create(HttpFactory::class),
        StreamFactoryInterface::class => create(HttpFactory::class),
        ContainerInterface::class => factory(
            static function (Container $container): ContainerInterface {
                return new ContainerDecorator($container);
            }
        ),
        ClientInterface::class => factory(
            static function (Container $container): ClientInterface {
                /** @var Client */
                return $container->make(Client::class, [
                    'config' => ['timeout' => 0.3],
                ]);
            }
        ),
    ]
);

try {
    /** @var Test $app */
    $app = $container->get(Test::class);
} catch (DependencyException | NotFoundException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

try {
    $_test = $app->get();
} catch (ContainerExceptionInterface | RequestException | ResponseException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $_test = TestResponse */

try {
    $test = $app->import('test');
} catch (ContainerExceptionInterface | RequestException | ResponseException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $test = TestResponse */

echo $test->name . PHP_EOL;
