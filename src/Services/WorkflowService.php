<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\WorkflowServiceInterface;

class WorkflowService extends BaseService implements WorkflowServiceInterface
{
    public function list(array $params = []): array
    {
        return $this->makeGetRequest('workflows', $params);
    }

    public function get(string $id, array $params = []): array
    {
        return $this->makeGetRequest("workflows/{$id}", $params);
    }

    public function create(array $data): array
    {
        return $this->makePostRequest('workflows', $data);
    }

    public function update(string $id, array $data): array
    {
        return $this->makePutRequest("workflows/{$id}", $data);
    }

    public function delete(string $id): array
    {
        return $this->makeDeleteRequest("workflows/{$id}");
    }

    public function activate(string $id): array
    {
        return $this->makePostRequest("workflows/{$id}/activate");
    }

    public function deactivate(string $id): array
    {
        return $this->makePostRequest("workflows/{$id}/deactivate");
    }

    public function transfer(string $id, string $destinationProjectId): array
    {
        return $this->makePutRequest("workflows/{$id}/transfer", [
            'destinationProjectId' => $destinationProjectId,
        ]);
    }

    public function getTags(string $id): array
    {
        return $this->makeGetRequest("workflows/{$id}/tags");
    }

    public function updateTags(string $id, array $tagIds): array
    {
        return $this->makePutRequest("workflows/{$id}/tags", ['tagIds' => $tagIds]);
    }
}
