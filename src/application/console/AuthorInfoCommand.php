<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\console;

use Error;
use Override;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\entity\author\AuthorUuid;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;
use kuaukutsu\ps\onion\infrastructure\repository\author\AuthorRepository;

/**
 * @psalm-internal kuaukutsu\ps\onion\application\console
 */
#[AsCommand(
    name: 'author:about',
    description: 'About author',
)]
final class AuthorInfoCommand extends Command
{
    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly AuthorRepository $repository,
        private readonly UuidValidator $uuidValidator,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    protected function configure(): void
    {
        $this->addArgument('uuid', InputArgument::REQUIRED, 'UUID author');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $model = $this->repository->get(
                $this->getArgumentUuid($input),
            );
        } catch (RuntimeException | Error $e) {
            $output->writeln($e->getMessage());
            $this->logger->preset(
                new LoggerExceptionPreset($e, ['input' => $input->getArguments()]),
                __METHOD__,
            );
            return Command::FAILURE;
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            $this->logger->preset(
                new LoggerExceptionPreset($e, ['input' => $input->getArguments()]),
                __METHOD__,
            );
            return Command::INVALID;
        }

        $output->writeln(sprintf('Authoe UUID: %s', $model->uuid->value));
        return Command::SUCCESS;
    }

    /**
     * @throws InvalidArgumentException если UUID не корректный
     */
    private function getArgumentUuid(InputInterface $input): AuthorUuid
    {
        $uuid = $input->getArgument('uuid');
        if (is_string($uuid) === false) {
            throw new InvalidArgumentException('UUID argument must be a non empty string.');
        }

        $this->uuidValidator->validate($uuid);

        /**
         * @var non-empty-string $uuid
         */
        return new AuthorUuid($uuid);
    }
}
