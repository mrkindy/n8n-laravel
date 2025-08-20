<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Strategies;

use Closure;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use MrKindy\N8NLaravel\Contracts\StrategyInterface;

class AsyncExecutionStrategy implements StrategyInterface
{
    public function execute(Closure $operation): mixed
    {
        return [
            'async' => true,
            'message' => 'Operation executed asynchronously',
            'result' => defer(fn() => $operation()),
            'timestamp' => date('c'),
        ];
    }

    public function getName(): string
    {
        return 'async';
    }
}
