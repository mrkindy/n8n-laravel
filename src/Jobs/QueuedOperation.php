<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Jobs;

use Closure;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueuedOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Closure $operation
    ) {
    }

    public function handle(): void
    {
        ($this->operation)();
    }
}
