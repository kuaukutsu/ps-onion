<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Override;
use Ramsey\Uuid\Uuid;
use kuaukutsu\ps\onion\domain\interface\RequestHttpContext;

final readonly class HttpContext implements RequestHttpContext
{
    /**
     * @var non-empty-string
     */
    private string $uuid;

    public function __construct(
        private float $timeout = 1.,
    ) {
        $this->uuid = Uuid::uuid7()->toString();
    }

    #[Override]
    public function getUuid(): string
    {
        return $this->uuid;
    }

    #[Override]
    public function getTimeout(): float
    {
        return $this->timeout;
    }
}
