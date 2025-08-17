<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Strategies;

use Closure;
use MrKindy\N8NLaravel\Contracts\StrategyInterface;

class AsyncExecutionStrategy implements StrategyInterface
{
    public function execute(Closure $operation): mixed
    {
        // For demonstration purposes, we'll simulate async execution
        // In a real implementation, you might use ReactPHP, Swoole, or other async libraries
        return [
            'async' => true,
            'message' => 'Operation executed asynchronously',
            'result' => $operation(),
            'timestamp' => date('c'),
        ];
    }

    public function getName(): string
    {
        return 'async';
    }
}
