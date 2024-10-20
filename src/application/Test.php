<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use Psr\Container\ContainerExceptionInterface;
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
     * @throws ContainerExceptionInterface
     */
    public function get(): TestResponse
    {
        // Logic: validate args (allowed types)
        return $this->service->get();
    }

    /**
     * @throws RequestException
     * @throws ResponseException
     * @throws ContainerExceptionInterface
     */
    public function import(string $name): TestResponse
    {
        // Logic: validate args (allowed types)
        assert($name !== '', 'non-empty-string');

        return $this->service->import($name);
    }
}
