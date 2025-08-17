<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;

class N8NResponseReceived
{
    use Dispatchable;

    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly array $response,
        public readonly float $duration,
        public readonly float $timestamp
    ) {
    }
}
