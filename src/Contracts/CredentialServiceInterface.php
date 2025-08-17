<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface CredentialServiceInterface
{
    public function create(array $data): array;
    
    public function delete(string $id): array;
    
    public function getSchema(string $credentialTypeName): array;
    
    public function transfer(string $id, string $destinationProjectId): array;
}
