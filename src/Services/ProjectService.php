<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\ProjectServiceInterface;

class ProjectService extends BaseService implements ProjectServiceInterface
{
    public function list(): array
    {
        return $this->makeGetRequest('projects');
    }

    public function create(array $data): array
    {
        return $this->makePostRequest('projects', $data);
    }

    public function delete(string $projectId): void
    {
        $this->makeDeleteRequest("projects/{$projectId}");
    }

    public function addUsers(string $projectId, array $users): array
    {
        return $this->makePostRequest("projects/{$projectId}/users", $users);
    }
}
