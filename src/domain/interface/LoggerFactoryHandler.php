<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use Monolog\Handler\HandlerInterface;

interface LoggerFactoryHandler
{
    public function makeHandler(ApplicationInterface $application): HandlerInterface;
}
