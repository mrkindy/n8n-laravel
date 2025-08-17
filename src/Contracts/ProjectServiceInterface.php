<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface ProjectServiceInterface
{
    public function list(): array;
    
    public function create(array $data): array;
    
    public function delete(string $projectId): void;
    
    public function addUsers(string $projectId, array $users): array;
}
