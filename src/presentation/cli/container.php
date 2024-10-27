<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use DI\Container;
use DI\Definition\Helper\DefinitionHelper;
use kuaukutsu\ps\onion\application\decorator\LoggerDecorator;
use kuaukutsu\ps\onion\domain\interface\Application;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;

use function DI\create;
use function DI\factory;
use function DI\get;

/**
 * @var array<string, DefinitionHelper> $definitions
 */
$definitions = require dirname(__DIR__) . '/bootstrap.php';

// console logger
$definitions[Application::class] = factory(
    static fn (): Application => new App()
);
$definitions[LoggerInterface::class] = create(LoggerDecorator::class)
    ->constructor(get(Application::class));

return new Container($definitions);
