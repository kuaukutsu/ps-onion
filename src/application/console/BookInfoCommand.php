<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\console;

use Override;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use kuaukutsu\ps\onion\application\validator\UuidValidator;
use kuaukutsu\ps\onion\domain\interface\LoggerInterface;
use kuaukutsu\ps\onion\domain\interface\RequestException;
use kuaukutsu\ps\onion\infrastructure\logger\preset\LoggerExceptionPreset;
use kuaukutsu\ps\onion\infrastructure\repository\book\BookRepository;

/**
 * @psalm-internal kuaukutsu\ps\onion\application\console
 */
#[AsCommand(
    name: 'app:book',
    description: 'About book'
)]
final class BookInfoCommand extends Command
{
    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly BookRepository $repository,
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
        $this->addArgument('uuid', InputArgument::REQUIRED, 'UUID book');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $book = $this->repository->get(
                $this->getArgumentUuid($input),
            );
        } catch (RequestException $e) {
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

        $output->writeln(sprintf('Book UUID: %s', $book->uuid));
        return Command::SUCCESS;
    }

    /**
     * @return non-empty-string
     * @throws InvalidArgumentException если UUID не корректный
     */
    private function getArgumentUuid(InputInterface $input): string
    {
        $uuid = $input->getArgument('uuid');
        if (is_string($uuid) === false) {
            throw new InvalidArgumentException('UUID argument must be a non empty string.');
        }

        $this->uuidValidator->validate($uuid);

        /**
         * @var non-empty-string
         */
        return $uuid;
    }
}
