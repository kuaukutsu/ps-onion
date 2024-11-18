<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity\author;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;

final readonly class AuthorInputDto implements EntityDto
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public string $name,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
