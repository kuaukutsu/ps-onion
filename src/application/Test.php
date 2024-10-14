<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use kuaukutsu\ps\onion\domain\entity\TestResponse;
use kuaukutsu\ps\onion\domain\exception\RequestException;
use kuaukutsu\ps\onion\domain\exception\ResponseException;
use kuaukutsu\ps\onion\domain\service\test\Service;

/**
 * @api
 */
final readonly class Test
{
    public function __construct(private Service $service)
    {
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function get(): TestResponse
    {
        return $this->service->get();
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     */
    public function import(): TestResponse
    {
        return $this->service->import('test');
    }
}
