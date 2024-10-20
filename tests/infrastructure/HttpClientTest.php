<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure;

use Override;
use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;
use kuaukutsu\ps\onion\infrastructure\http\HttpClient;
use kuaukutsu\ps\onion\tests\Container;

use function DI\factory;

final class HttpClientTest extends TestCase
{
    use Container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testSend(): void
    {
        $httpClient = self::get(HttpClient::class);
        $response = $httpClient->send(
            new EntityRequestStub(),
            new RequestContextStub('uuid-test'),
        );

        self::assertInstanceOf(EntityStub::class, $response);
        self::assertEquals('uuid-test', $response->name);
    }

    #[Override]
    protected function setUp(): void
    {
        self::setDefinition(ClientInterface::class, factory(
            static function (): ClientInterface {
                return new class implements ClientInterface {
                    public function sendRequest(RequestInterface $request): ResponseInterface
                    {
                        $requestId = current($request->getHeader('X-Request-Id'));
                        $responseDataJson = <<<JSON
{
  "name": "$requestId"
}
JSON;

                        return new Response(200, [], $responseDataJson);
                    }
                };
            }
        ));
    }
}
