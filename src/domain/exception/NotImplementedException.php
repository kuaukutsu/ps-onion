<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use RuntimeException;

final class NotImplementedException extends RuntimeException
{
    public function __construct(string $message = 'Not implemented.')
    {
        parent::__construct($message);
    }
}
