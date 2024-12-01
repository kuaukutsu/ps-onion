<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use Override;
use RuntimeException;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\domain\interface\ApplicationInterface;
use kuaukutsu\ps\onion\domain\interface\ApplicationSetup;
use kuaukutsu\ps\onion\domain\interface\ContainerInterface;
use kuaukutsu\ps\onion\infrastructure\repository\RepositorySetup;

/**
 * @api
 */
final readonly class Setup implements ApplicationSetup
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    #[Override]
    public function run(ApplicationInterface $application): void
    {
        // Repository
        $this->container
            ->make(RepositorySetup::class)
            ->run($application);
        // Other infrastructure
    }
}
