<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\ExecutionServiceInterface;

class ExecutionService extends BaseService implements ExecutionServiceInterface
{
    public function list(array $params = []): array
    {
        return $this->makeGetRequest('executions', $params);
    }

    public function get(string $id, array $params = []): array
    {
        return $this->makeGetRequest("executions/{$id}", $params);
    }

    public function delete(string $id): array
    {
        return $this->makeDeleteRequest("executions/{$id}");
    }
}
