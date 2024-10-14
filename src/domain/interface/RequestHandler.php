<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use kuaukutsu\ps\onion\domain\exception\RequestException;
use kuaukutsu\ps\onion\domain\exception\ResponseException;

interface RequestHandler
{
    /**
     * @template TResponse of Response
     * @param Request<TResponse> $request
     * @return TResponse
     * @throws RequestException
     * @throws ResponseException
     * @noinspection PhpDocSignatureInspection
     */
    public function send(Request $request, RequestContext $context): Response;
}
