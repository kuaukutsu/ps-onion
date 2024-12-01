<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\setup;

use Throwable;
use kuaukutsu\ps\onion\application\Application;
use kuaukutsu\ps\onion\application\Setup;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

try {
    $application = new Application('onion.setup', '0.0.2');
    $setup = new Setup(
        $application->getContainer(),
    );

    $setup->run($application);
} catch (Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-1);
}
