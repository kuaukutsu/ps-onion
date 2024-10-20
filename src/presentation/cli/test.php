<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use InvalidArgumentException;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use GuzzleHttp\Psr7\HttpFactory;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\GuzzleDecorator;
use kuaukutsu\ps\onion\application\Test;
use kuaukutsu\ps\onion\domain\entity\TestResponse;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;

use function DI\autowire;
use function DI\create;
use function DI\factory;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

$container = new Container(
    [
        ContainerInterface::class => factory(
            static function (Container $container): ContainerInterface {
                return new ContainerDecorator($container);
            }
        ),
        RequestFactoryInterface::class => create(HttpFactory::class),
        StreamFactoryInterface::class => create(HttpFactory::class),
        ClientInterface::class => autowire(GuzzleDecorator::class),
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
} catch (ContainerExceptionInterface | RequestException | InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $_test = TestResponse */

try {
    $test = $app->import('test');
} catch (ContainerExceptionInterface | RequestException | InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $test = TestResponse */

echo $test->name . PHP_EOL;
