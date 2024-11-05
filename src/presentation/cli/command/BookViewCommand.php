<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli\command;

use Error;
use Override;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use kuaukutsu\ps\onion\application\Bookshelf;

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\cli
 */
#[AsCommand(
    name: 'book:view',
    description: 'Book view',
)]
final class BookViewCommand extends Command
{
    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly Bookshelf $bookshelf,
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
            $book = $this->bookshelf->get(
                $this->getArgumentUuid($input),
            );
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return Command::INVALID;
        } catch (ClientExceptionInterface | Error $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
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

        /**
         * @var non-empty-string
         */
        return $uuid;
    }
}
