<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface UserServiceInterface
{
    public function list(array $params = []): array;
    
    public function get(string $id, array $params = []): array;
    
    public function create(array $users): array;
    
    public function delete(string $id): void;
    
    public function changeRole(string $id, array $roleData): array;
}
