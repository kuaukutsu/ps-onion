<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use Fig\Http\Message\RequestMethodInterface;

interface Request extends RequestMethodInterface
{
    public function getMethod(): string;

    public function getUri(): string;

    public function getBody(): string;
}
