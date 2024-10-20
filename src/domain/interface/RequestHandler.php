<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\interface;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;

interface RequestHandler
{
    /**
     * @template TResponse of Response
     * @param RequestEntity<TResponse> $requestEntity
     * @return TResponse
     * @throws ContainerExceptionInterface
     * @throws RequestException
     * @throws InvalidArgumentException
     * @noinspection PhpDocSignatureInspection
     */
    public function send(RequestEntity $requestEntity, RequestContext $context): Response;
}
