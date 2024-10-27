<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\http;

use DI\Container;
use DI\Definition\Helper\DefinitionHelper;

/**
 * @var array<string, DefinitionHelper> $definitions
 */
$definitions = require dirname(__DIR__) . '/bootstrap.php';

return new Container($definitions);
