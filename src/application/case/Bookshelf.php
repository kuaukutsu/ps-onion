<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\case;

use TypeError;
use LogicException;
use InvalidArgumentException;
use kuaukutsu\ps\onion\application\case\book\Import;
use kuaukutsu\ps\onion\application\case\book\View;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\application\output\BookDto;
use kuaukutsu\ps\onion\domain\exception\InfrastructureException;
use kuaukutsu\ps\onion\domain\exception\NotFoundException;

/**
 * @api
 * @note: Interactor
 */
final readonly class Bookshelf
{
    public function __construct(
        private Import $import,
        private View $view,
    ) {
    }

    /**
     * @param non-empty-string $isbn
     * @throws TypeError is output data error
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     * @throws InvalidArgumentException validation data
     */
    public function get(string $isbn): BookDto
    {
        return $this->view->getByISBN($isbn);
    }

    /**
     * @throws LogicException is input data not valid
     * @throws NotFoundException
     * @throws InfrastructureException
     */
    public function find(BookInput $input): BookDto
    {
        return $this->view->getByName($input);
    }

    /**
     * @throws TypeError is output data error
     * @throws LogicException is input data not valid
     * @throws InfrastructureException
     */
    public function import(BookInput $input): BookDto
    {
        return $this->import->push($input);
    }
}
