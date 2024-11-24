<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;

/**
 * @psalm-internal kuaukutsu\ps\onion\application
 */
final readonly class BookImportValidator
{
    public function __construct(
        private AuthorValidator $authorValidator,
        private BookTitleValidator $bookTitleValidator,
    ) {
    }

    /**
     * @throws LogicException
     */
    public function prepareTitle(BookInput $input): BookTitle
    {
        /** @var non-empty-string $title */
        $title = trim($input->title);
        $this->bookTitleValidator->validate($title);

        return new BookTitle($title);
    }

    /**
     * @throws LogicException
     */
    public function prepareAuthor(BookInput $input): ?BookAuthor
    {
        if ($input->author !== null) {
            return new BookAuthor(
                $this->authorValidator->prepare($input->author)->name
            );
        }

        return null;
    }
}
