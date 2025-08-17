<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Strategies;

use Closure;
use MrKindy\N8NLaravel\Contracts\StrategyInterface;

class SyncExecutionStrategy implements StrategyInterface
{
    public function execute(Closure $operation): mixed
    {
        return $operation();
    }

    public function getName(): string
    {
        return 'sync';
    }
}
