<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\repository\book;

use Override;
use LogicException;
use kuaukutsu\ps\onion\domain\entity\book\BookDto;
use kuaukutsu\ps\onion\domain\exception\NotImplementedException;
use kuaukutsu\ps\onion\domain\interface\RequestEntity;
use kuaukutsu\ps\onion\domain\interface\StreamDecode;
use kuaukutsu\ps\onion\infrastructure\serialize\EntityMapper;

/**
 * @implements RequestEntity<BookDto>
 * @psalm-internal kuaukutsu\ps\onion\infrastructure\repository
 */
final readonly class BookFindByPropertyRequest implements RequestEntity
{
    private string $query;

    /**
     * @param non-empty-string|null $author
     * @param non-empty-string|null $title
     * @throws LogicException
     */
    public function __construct(
        ?string $author = null,
        ?string $title = null,
    ) {
        $this->query = $this->prepareArgsToQueryString(
            [
                'author' => $author,
                'title' => $title,
            ]
        );
    }

    #[Override]
    public function getMethod(): string
    {
        return self::METHOD_GET;
    }

    #[Override]
    public function getUri(): string
    {
        return 'https://webhook.site/8cabc407-a3f0-41b3-8f53-b5f1edcff4f0?' . $this->query;
    }

    /**
     * @throws NotImplementedException
     */
    #[Override]
    public function getBody(): never
    {
        throw new NotImplementedException();
    }

    #[Override]
    public function makeResponse(StreamDecode $stream): BookDto
    {
        return EntityMapper::denormalize(BookDto::class, $stream->decode());
    }

    /**
     * @param array<non-empty-string, non-empty-string|null> $args
     * @throws LogicException
     */
    private function prepareArgsToQueryString(array $args): string
    {
        $conditions = [];
        foreach ($args as $key => $value) {
            if ($value !== null) {
                $conditions[$key] = $value;
            }
        }

        if ($conditions === []) {
            throw new LogicException('Property must not be empty.');
        }

        return http_build_query($conditions);
    }
}