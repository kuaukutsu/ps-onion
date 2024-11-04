<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\interface\RequestException;

/**
 * @var Container $container
 */
$container = require __DIR__ . '/container.php';

try {
    /** @var Bookshelf $app */
    $app = $container->get(Bookshelf::class);
} catch (DependencyException | NotFoundException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

try {
    $_test = $app->get('8cabc407-a3f0-41b3-8f53-b5f1edcff4f0');
} catch (RequestException | InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $_test = BookDto */

try {
    $test = $app->import('test', 'testov');
} catch (RequestException | InvalidArgumentException | LogicException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

/** @psalm-check-type-exact $test = BookDto */

echo $test->uuid . PHP_EOL;
echo $test->title . PHP_EOL;
echo $test->author . PHP_EOL;
