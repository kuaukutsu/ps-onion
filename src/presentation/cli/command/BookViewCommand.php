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
use kuaukutsu\ps\onion\application\case\Bookshelf;
use kuaukutsu\ps\onion\presentation\cli\output\BookMessage;

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
        $this->addArgument('isbn', InputArgument::REQUIRED, 'ISBN book');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $book = $this->bookshelf->get(
                $this->getArgumentIsbn($input),
            );
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return Command::INVALID;
        } catch (Exception | Error $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln(
            BookMessage::fromBook($book)->output()
        );
        return Command::SUCCESS;
    }

    /**
     * @return non-empty-string
     * @throws InvalidArgumentException если UUID не корректный
     */
    private function getArgumentIsbn(InputInterface $input): string
    {
        $isbn = $input->getArgument('isbn');
        if (is_string($isbn) === false) {
            throw new InvalidArgumentException('ISBN argument must be a non empty string.');
        }

        /**
         * @var non-empty-string
         */
        return $isbn;
    }
}
