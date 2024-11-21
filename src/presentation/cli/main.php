<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use kuaukutsu\ps\onion\application\Application as OnionApplication;
use kuaukutsu\ps\onion\presentation\cli\command\AuthorCreateCommand;
use kuaukutsu\ps\onion\presentation\cli\command\AuthorViewCommand;
use kuaukutsu\ps\onion\presentation\cli\command\BookFindCommand;
use kuaukutsu\ps\onion\presentation\cli\command\BookImportCommand;
use kuaukutsu\ps\onion\presentation\cli\command\BookViewCommand;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

try {
    $application = new OnionApplication('onion.cli', '0.0.2');
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(Command::FAILURE);
}

$console = new Application();
$console->setCommandLoader(
    new ContainerCommandLoader(
        $application->getContainer(),
        [
            'author:create' => AuthorCreateCommand::class,
            'author:view' => AuthorViewCommand::class,
            'book:import' => BookImportCommand::class,
            'book:view' => BookViewCommand::class,
            'book:find' => BookFindCommand::class,
        ],
    )
);

try {
    exit($console->run());
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(Command::FAILURE);
}
