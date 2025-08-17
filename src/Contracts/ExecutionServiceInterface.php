<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface ExecutionServiceInterface
{
    public function list(array $params = []): array;
    
    public function get(string $id, array $params = []): array;
    
    public function delete(string $id): array;
}
