<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application;

use RuntimeException;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\ps\onion\domain\exception\DbException;
use kuaukutsu\ps\onion\domain\exception\DbStatementException;
use kuaukutsu\ps\onion\infrastructure\repository\RepositorySetup;

/**
 * @api
 */
final readonly class Setup
{
    public function __construct(private Application $application)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws DbException connection failed.
     * @throws DbStatementException query failed.
     */
    public function run(): void
    {
        $container = $this->application->getContainer();

        // Repository
        $container
            ->make(RepositorySetup::class)
            ->run($this->application);

        // Other infrastructure
    }
}
