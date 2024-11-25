<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\tests\infrastructure\http;

use Override;
use kuaukutsu\ps\onion\domain\interface\RequestContext;

final readonly class RequestContextStub implements RequestContext
{
    /**
     * @param non-empty-string $uuid
     */
    public function __construct(private string $uuid)
    {
    }

    #[Override]
    public function getUuid(): string
    {
        return $this->uuid;
    }

    #[Override]
    public function getTimeout(): float
    {
        return .5;
    }
}
