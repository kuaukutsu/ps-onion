<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\web;

use Override;
use InvalidArgumentException;
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\GuzzleDecorator;
use kuaukutsu\ps\onion\application\decorator\LoggerDecorator;
use kuaukutsu\ps\onion\domain\interface\Application as DomainApplication;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;

use function DI\create;

/**
 * @note заглушка.
 */
final readonly class Application implements DomainApplication
{
    private LoggerInterface $logger;

    private ContainerInterface $container;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(Container $container)
    {
        $this->logger = new LoggerDecorator($this);
        $this->container = new ContainerDecorator($container);

        $this->setDefinitions($container);
    }

    #[Override]
    public function getName(): string
    {
        return 'onion.web';
    }

    #[Override]
    public function getVersion(): string
    {
        return '0.0.1';
    }

    #[Override]
    public function getRuntime(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'runtime';
    }

    public function run(): ResponseInterface
    {
        return new Response(201);
    }

    private function setDefinitions(Container $container): void
    {
        $container->set(
            ContainerInterface::class,
            $this->container,
        );

        $container->set(
            LoggerInterface::class,
            $this->logger,
        );

        $container->set(
            ClientInterface::class,
            create(GuzzleDecorator::class)
                ->constructor($this->container),
        );
    }
}
