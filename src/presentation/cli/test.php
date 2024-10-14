<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use kuaukutsu\ps\onion\application\Test;
use kuaukutsu\ps\onion\domain\entity\TestResponse;
use kuaukutsu\ps\onion\domain\exception\RequestException;
use kuaukutsu\ps\onion\domain\exception\ResponseException;

use function DI\create;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

$container = new Container(
    [
        ClientInterface::class => create(Client::class),
        RequestFactoryInterface::class => create(HttpFactory::class),
        StreamFactoryInterface::class => create(HttpFactory::class),
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
} catch (RequestException | ResponseException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $_test = TestResponse */

try {
    $test = $app->import();
} catch (RequestException | ResponseException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $test = TestResponse */

echo $test->name . PHP_EOL;

/**
 * Request and Response в одном пространстве.
 * Для Request необходимо:
 * - RequestFactoryInterface -> RequestInterface
 * - дополнительные модули для обработки Response? НЕТ
 * Для Response необходимо:
 * - входящий набор данных
 */
