<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface Request
{
    public function getMethod(): string;

    public function getUri(): string;

    public function getBody(): string;
}
