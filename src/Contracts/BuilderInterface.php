<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface BuilderInterface
{
    public function build(): array;
    
    public function reset(): static;
}
