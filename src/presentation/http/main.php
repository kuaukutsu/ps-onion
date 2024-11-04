<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use InvalidArgumentException;
use DI\Container;
use DI\Definition\Helper\DefinitionHelper;
use kuaukutsu\ps\onion\application\web\Application;

/**
 * @var array<string, DefinitionHelper> $definitions
 */
$definitions = require dirname(__DIR__) . '/bootstrap.php';

try {
    $app = new Application(new Container($definitions));
} catch (InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}

$app->run();
