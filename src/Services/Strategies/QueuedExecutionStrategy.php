<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Strategies;

use Closure;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MrKindy\N8NLaravel\Contracts\StrategyInterface;

class QueuedExecutionStrategy implements StrategyInterface
{
    public function execute(Closure $operation): mixed
    {
        QueuedOperation::dispatch($operation);
        
        return ['queued' => true, 'message' => 'Operation queued for execution'];
    }

    public function getName(): string
    {
        return 'queued';
    }
}

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
