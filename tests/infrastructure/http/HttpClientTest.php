<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\http;

use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Psr7\Response;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\RequestContext;
use kuaukutsu\ps\onion\infrastructure\http\HttpClient;
use kuaukutsu\ps\onion\tests\Container;
use kuaukutsu\ps\onion\tests\infrastructure\repository\EntityDtoStub;
use kuaukutsu\ps\onion\tests\infrastructure\repository\EntityRequestStub;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function DI\factory;

final class HttpClientTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testSend(): void
    {
        $httpClient = self::get(HttpClient::class);
        $response = $httpClient->send(
            new EntityRequestStub(),
            new RequestContextStub('uuid-test'),
        );

        self::assertInstanceOf(EntityDtoStub::class, $response);
        self::assertEquals('uuid-test', $response->name);
    }

    #[Override]
    protected function setUp(): void
    {
        self::setDefinition(ClientInterface::class, factory(
            static function (): ClientInterface {
                return new class implements ClientInterface {
                    public function send(RequestInterface $request, RequestContext $context): ResponseInterface
                    {
                        $requestId = current($request->getHeader('X-Request-Id'));
                        $responseDataJson = <<<JSON
{
  "name": "$requestId"
}
JSON;

                        return new Response(200, ['Content-Type' => ['application/json']], $responseDataJson);
                    }
                };
            }
        ));
    }
}
