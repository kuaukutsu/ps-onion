<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use InvalidArgumentException;

interface Request
{
    public function getMethod(): string;

    public function getUri(): string;

    /**
     * @throws InvalidArgumentException convert data to string
     */
    public function getBody(): string;
}
