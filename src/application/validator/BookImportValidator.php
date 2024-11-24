<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\application\validator;

use LogicException;
use kuaukutsu\ps\onion\application\input\BookInput;
use kuaukutsu\ps\onion\domain\entity\book\BookAuthor;
use kuaukutsu\ps\onion\domain\entity\book\BookTitle;
use kuaukutsu\ps\onion\domain\entity\book\BookFind;

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
    public function prepare(BookInput $input): BookFind
    {
        /** @var non-empty-string $title */
        $title = trim($input->title);
        $this->bookTitleValidator->validate($title);

        $author = null;
        if ($input->author !== null) {
            $author = new BookAuthor(
                $this->authorValidator->prepare($input->author)->name
            );
        }

        return new BookFind(
            title: new BookTitle($title),
            author: $author,
        );
    }
}
