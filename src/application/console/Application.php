<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\console;

use Override;
use Exception;
use InvalidArgumentException;
use DI\Container;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\GuzzleDecorator;
use kuaukutsu\ps\onion\application\decorator\LoggerDecorator;
use kuaukutsu\ps\onion\domain\interface\Application as DomainApplication;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;

use function DI\create;

/**
 * Entrypoint: точка конфигурирования и запуск приложения.
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
        return 'onion.cli';
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

    public function run(): int
    {
        try {
            $commands = [
                $this->container->make(BookInfoCommand::class),
            ];
        } catch (ContainerExceptionInterface | InvalidArgumentException $e) {
            $this->logger->preset(new LoggerExceptionPreset($e), __METHOD__);
            return Command::FAILURE;
        }

        $application = new SymfonyApplication();
        $application->addCommands($commands);

        try {
            return $application->run();
        } catch (Exception $e) {
            $this->logger->preset(new LoggerExceptionPreset($e), __METHOD__);
            return Command::FAILURE;
        }
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
