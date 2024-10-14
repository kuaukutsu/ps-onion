<?php

declare(strict_types=1);

namespace kuaukutsu\ps\onion\infrastructure\http;

use Override;
use Ramsey\Uuid\Uuid;
use kuaukutsu\ps\onion\domain\interface\RequestContext;

final readonly class HttpContext implements RequestContext
{
    /**
     * @var non-empty-string
     */
    private string $uuid;

    public function __construct(
        public float $timeout = 1.,
    ) {
        $this->uuid = Uuid::uuid7()->toString();
    }

    #[Override]
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
