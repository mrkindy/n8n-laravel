<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface WorkflowServiceInterface
{
    public function list(array $params = []): array;
    
    public function get(string $id, array $params = []): array;
    
    public function create(array $data): array;
    
    public function update(string $id, array $data): array;
    
    public function delete(string $id): array;
    
    public function activate(string $id): array;
    
    public function deactivate(string $id): array;
    
    public function transfer(string $id, string $destinationProjectId): array;
    
    public function getTags(string $id): array;
    
    public function updateTags(string $id, array $tagIds): array;
}
