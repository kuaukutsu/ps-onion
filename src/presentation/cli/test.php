<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use InvalidArgumentException;
use LogicException;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use GuzzleHttp\Psr7\HttpFactory;
use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\GuzzleDecorator;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\domain\entity\Book;
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
        UuidFactoryInterface::class => create(UuidFactory::class),
        ValidatorInterface::class => create(Validator::class),
    ]
);

try {
    /** @var Bookshelf $app */
    $app = $container->get(Bookshelf::class);
} catch (DependencyException | NotFoundException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

try {
    $_test = $app->get('5669bc32-92b7-4b31-9bc7-203b9d11438d');
} catch (ContainerExceptionInterface | RequestException | InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $_test = Book */

try {
    $test = $app->import('test', 'testov');
} catch (ContainerExceptionInterface | RequestException | InvalidArgumentException | LogicException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $test = Book */

echo $test->uuid . PHP_EOL;
echo $test->title . PHP_EOL;
echo $test->author . PHP_EOL;
