<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use Exception;
use Symfony\Component\Console\Command\Command;
use kuaukutsu\ps\onion\application\Application as OnionApplication;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

try {
    $_application = new OnionApplication('onion.web', '0.0.2');
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(Command::FAILURE);
}
