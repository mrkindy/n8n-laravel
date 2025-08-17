<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

use Closure;

interface StrategyInterface
{
    public function execute(Closure $operation): mixed;
    
    public function getName(): string;
}
