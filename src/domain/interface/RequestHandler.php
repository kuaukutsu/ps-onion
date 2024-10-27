<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

interface RequestHandler
{
    /**
     * @template TResponse of Response
     * @param RequestEntity<TResponse> $requestEntity
     * @return TResponse
     * @throws RequestException
     * @noinspection PhpDocSignatureInspection
     */
    public function send(RequestEntity $requestEntity, RequestContext $context): Response;
}
