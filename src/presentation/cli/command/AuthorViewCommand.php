<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli\command;

use Override;
use Error;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use kuaukutsu\ps\onion\application\case\AuthorIndex;
use kuaukutsu\ps\onion\presentation\cli\output\AuthorMessage;

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\cli
 */
#[AsCommand(
    name: 'author:view',
    description: 'About author',
)]
final class AuthorViewCommand extends Command
{
    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly AuthorIndex $index,
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
            $author = $this->index->get(
                $this->getArgumentUuid($input),
            );
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return Command::INVALID;
        } catch (Exception | Error $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln(
            AuthorMessage::fromBook($author)->output()
        );
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

        /**
         * @var non-empty-string $uuid
         */
        return $uuid;
    }
}
