<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\db;

/**
 * @link https://www.php.net/manual/en/pdo.drivers.php
 */
enum DbConnectionDriver: string
{
    case PDO_SQLITE = 'sqlite';

    case PDO_PGSQL = 'pgsql';

    case UNSUPPORTED = 'unsupported';
}
