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

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\cli
 */
#[AsCommand(
    name: 'book:import',
    description: 'Push book in shelf',
)]
final class BookImportCommand extends Command
{
    /**
     * @throws LogicException
     */
    public function __construct(
        private readonly Bookshelf $index,
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
            $book = $this->index->import(
                $this->getArgumentData($input),
            );
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return Command::INVALID;
        } catch (Exception | Error $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln(sprintf('Book UUID: %s', $book->uuid->value));
        return Command::SUCCESS;
    }

    /**
     * @return array{"title": string, "author": string}
     * @throws InvalidArgumentException если UUID не корректный
     */
    private function getArgumentData(InputInterface $input): array
    {
        $title = $input->getOption('title');
        if (is_string($title) === false) {
            throw new InvalidArgumentException('Title argument must be string.');
        }

        $author = $input->getOption('author');
        if (is_string($author) === false) {
            throw new InvalidArgumentException('Author argument must be string.');
        }

        return [
            'title' => $title,
            'author' => $author,
        ];
    }
}
