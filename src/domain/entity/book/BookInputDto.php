<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\book;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class BookInputDto implements EntityDto
{
    /**
     * @param non-empty-string $title
     * @param non-empty-string|null $description
     */
    public function __construct(
        public string $title,
        public ?string $description = null,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
