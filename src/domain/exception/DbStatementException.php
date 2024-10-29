<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\exception;

use RuntimeException;

final class DbStatementException extends RuntimeException
{
    /**
     * @param array{0: string, 1: int, 2: string} $errorInfo
     * @link https://www.php.net/manual/en/pdostatement.errorinfo.php
     */
    public function __construct(array $errorInfo, string $query)
    {
        parent::__construct(
            sprintf('[%s] %s, query: %s', $errorInfo[0], $errorInfo[2], $query)
        );
    }
}
