<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface VariableServiceInterface
{
    public function list(array $params = []): array;
    
    public function create(array $data): array;
    
    public function update(string $id, array $data): array;
    
    public function delete(string $id): void;
}
