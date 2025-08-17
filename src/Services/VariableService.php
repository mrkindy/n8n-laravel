<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\VariableServiceInterface;

class VariableService extends BaseService implements VariableServiceInterface
{
    public function list(array $params = []): array
    {
        return $this->makeGetRequest('variables', $params);
    }

    public function create(array $data): array
    {
        return $this->makePostRequest('variables', $data);
    }

    public function update(string $id, array $data): array
    {
        return $this->makePutRequest("variables/{$id}", $data);
    }

    public function delete(string $id): void
    {
        $this->makeDeleteRequest("variables/{$id}");
    }
}
