<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\domain\entity;

use Override;
use kuaukutsu\ps\onion\domain\interface\EntityDto;
use kuaukutsu\ps\onion\domain\interface\Response;

/**
 * @psalm-internal kuaukutsu\ps\onion\domain\entity
 */
final readonly class ExampleResponse implements EntityDto, Response
{
    public function __construct(
        public string $name,
    ) {
    }

    #[Override]
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
