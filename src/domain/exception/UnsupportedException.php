<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use Exception;

final class UnsupportedException extends Exception
{
    public function __construct(string $message = 'Unsupported operation.')
    {
        parent::__construct($message);
    }
}
