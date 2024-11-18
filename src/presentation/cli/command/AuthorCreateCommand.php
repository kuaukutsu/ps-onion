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
use kuaukutsu\ps\onion\application\AuthorIndex;

/**
 * @psalm-internal kuaukutsu\ps\onion\presentation\cli
 */
#[AsCommand(
    name: 'author:create',
    description: 'Push author in current index',
)]
final class AuthorCreateCommand extends Command
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
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name author');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $author = $this->index->push(
                $this->getArgumentData($input),
            );
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return Command::INVALID;
        } catch (Exception | Error $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln(sprintf('Authoe UUID: %s', $author->uuid->value));
        return Command::SUCCESS;
    }

    /**
     * @return array{"name": string}
     * @throws InvalidArgumentException если UUID не корректный
     */
    private function getArgumentData(InputInterface $input): array
    {
        $name = $input->getOption('name');
        if (is_string($name) === false) {
            throw new InvalidArgumentException('Name argument must be a string.');
        }

        return [
            'name' => $name,
        ];
    }
}