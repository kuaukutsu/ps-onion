<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use Override;
use InvalidArgumentException;
use DI\Container;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use GuzzleHttp\Psr7\HttpFactory;
use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;
use kuaukutsu\ps\onion\application\decorator\CacheDecorator;
use kuaukutsu\ps\onion\application\decorator\ContainerDecorator;
use kuaukutsu\ps\onion\application\decorator\GuzzleDecorator;
use kuaukutsu\ps\onion\application\decorator\LoggerDecorator;
use kuaukutsu\ps\onion\domain\entity\author\Author;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\AuthorRepository;
use kuaukutsu\ps\onion\domain\interface\BookRepository;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\domain\interface\ClientInterface;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\db\ConnectionMap;
use kuaukutsu\ps\onion\infrastructure\db\pdo\SqliteConnection;
use kuaukutsu\ps\onion\infrastructure\repository\author\Repository as RepositoryAuthor;
use kuaukutsu\ps\onion\infrastructure\repository\book\Repository as RepositoryBook;

use function DI\create;
use function DI\factory;

/**
 * @api
 */
final readonly class Application implements ApplicationInterface
{
    private ContainerInterface $container;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $version
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $name,
        private string $version,
    ) {
        $container = new Container(
            [
                RequestFactoryInterface::class => create(HttpFactory::class),
                StreamFactoryInterface::class => create(HttpFactory::class),
                UuidFactoryInterface::class => create(UuidFactory::class),
                ValidatorInterface::class => create(Validator::class),
                CacheInterface::class => create(CacheDecorator::class),
            ]
        );

        $this->container = new ContainerDecorator($container);
        $this->setDefinitions($container);
        $this->setRepository($container);
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function getVersion(): string
    {
        return $this->version;
    }

    #[Override]
    public function getRuntime(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'runtime';
    }

    public function getContainer(): PsrContainerInterface
    {
        return $this->container;
    }

    private function setDefinitions(Container $container): void
    {
        $container->set(
            ApplicationInterface::class,
            $this,
        );

        $container->set(
            ContainerInterface::class,
            $this->container,
        );

        $container->set(
            LoggerInterface::class,
            factory(
                fn(): LoggerInterface => new LoggerDecorator($this)
            ),
        );

        $container->set(
            ClientInterface::class,
            factory(
                fn(): ClientInterface => new GuzzleDecorator($this->container)
            )
        );
    }

    private function setRepository(Container $container): void
    {
        $container->set(
            ConnectionMap::class,
            new ConnectionMap(
                new SqliteConnection(
                    Author::class,
                    "sqlite:{$this->getRuntime()}/sqlite/author.sq3"
                )
            )
        );

        $container->set(
            AuthorRepository::class,
            create(RepositoryAuthor::class),
        );

        $container->set(
            BookRepository::class,
            create(RepositoryBook::class),
        );
    }
}
