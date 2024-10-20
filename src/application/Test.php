<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\ps\onion\domain\entity\TestResponse;
use kuaukutsu\ps\onion\domain\interface\RequestException;
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
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function get(): TestResponse
    {
        // Logic: validate args (allowed types)
        return $this->service->get();
    }

    /**
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function import(string $name): TestResponse
    {
        // Logic: validate args (allowed types)
        assert($name !== '', 'non-empty-string');

        return $this->service->import($name);
    }
}
