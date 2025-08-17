<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\TagServiceInterface;

class TagService extends BaseService implements TagServiceInterface
{
    public function list(array $params = []): array
    {
        return $this->makeGetRequest('tags', $params);
    }

    public function get(string $id): array
    {
        return $this->makeGetRequest("tags/{$id}");
    }

    public function create(array $data): array
    {
        return $this->makePostRequest('tags', $data);
    }

    public function update(string $id, array $data): array
    {
        return $this->makePutRequest("tags/{$id}", $data);
    }

    public function delete(string $id): array
    {
        return $this->makeDeleteRequest("tags/{$id}");
    }
}
