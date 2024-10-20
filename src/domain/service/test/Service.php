<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service\test;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\ps\onion\domain\entity\TestImportData;
use kuaukutsu\ps\onion\domain\entity\TestImportRequest;
use kuaukutsu\ps\onion\domain\entity\TestRequest;
use kuaukutsu\ps\onion\domain\entity\TestResponse;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\infrastructure\http\HttpClient;
use kuaukutsu\ps\onion\infrastructure\http\HttpContext;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class Service
{
    public function __construct(private HttpClient $client)
    {
    }

    /**
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function get(): TestResponse
    {
        // Logic: validate domain rule
        return $this->client->send(
            new TestRequest(),
            new HttpContext(),
        );
    }

    /**
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function import(string $name): TestResponse
    {
        // Logic: validate domain rule
        return $this->client->send(
            new TestImportRequest(
                new TestImportData(
                    name: $name,
                    time: time(),
                )
            ),
            new HttpContext(),
        );
    }
}
