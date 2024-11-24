<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use RuntimeException;

final class ConflictException extends RuntimeException
{
    public function __construct(string $message = 'Conflict.')
    {
        parent::__construct($message);
    }
}
