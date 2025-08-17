<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Throwable;

class N8NRequestFailed
{
    use Dispatchable;

    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly array $headers,
        public readonly array $payload,
        public readonly Throwable $exception,
        public readonly float $timestamp
    ) {
    }
}
