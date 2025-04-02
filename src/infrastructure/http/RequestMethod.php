<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
}
