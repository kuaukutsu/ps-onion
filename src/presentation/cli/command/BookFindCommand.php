<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\presentation\cli\command;

use Error;
use Exception;
use Override;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use kuaukutsu\ps\onion\application\Bookshelf;
use kuaukutsu\ps\onion\application\input\AuthorInput;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\presentation\cli\output\BookMessage;

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\cli
 */
#[AsCommand(
    name: 'book:find',
    description: 'Book search',
)]
final class BookFindCommand extends Command
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
        $this
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Name book')
            ->addOption('author', null, InputOption::VALUE_REQUIRED, 'Name author');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $book = $this->bookshelf->find(
                $this->getArgumentData($input),
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
     * @throws InvalidArgumentException если UUID не корректный
     */
    private function getArgumentData(InputInterface $input): BookInput
    {
        $title = $input->getOption('title');
        if (is_string($title) === false) {
            throw new InvalidArgumentException('Title argument must be string.');
        }

        $author = $input->getOption('author');
        if (is_string($author) === false) {
            throw new InvalidArgumentException('Author argument must be string.');
        }

        return new BookInput(
            title: $title,
            author: new AuthorInput(name: $author),
        );
    }
}
