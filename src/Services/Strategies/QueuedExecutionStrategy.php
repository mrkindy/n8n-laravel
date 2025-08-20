<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Strategies;

use Closure;
use MrKindy\N8NLaravel\Contracts\StrategyInterface;
use MrKindy\N8NLaravel\Jobs\QueuedOperation;

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
