<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\service;

use kuaukutsu\ps\onion\domain\entity\author\Author;

final readonly class AuthorSearch
{
    /**
     * @param array<string, Author> $listAuthor
     * @param null|callable(Author $author):bool $ctxFilter
     */
    public function find(array $listAuthor, ?callable $ctxFilter = null): ?Author
    {
        if ($listAuthor === []) {
            return null;
        }

        if (is_callable($ctxFilter)) {
            foreach ($listAuthor as $author) {
                if ($ctxFilter($author)) {
                    return $author;
                }
            }
        }

        // @note: Логика выбора нужного автора из списка если поиск вернул больше одного.
        return current($listAuthor);
    }
}
