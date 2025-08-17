<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\UserServiceInterface;

class UserService extends BaseService implements UserServiceInterface
{
    public function list(array $params = []): array
    {
        return $this->makeGetRequest('users', $params);
    }

    public function get(string $id, array $params = []): array
    {
        return $this->makeGetRequest("users/{$id}", $params);
    }

    public function create(array $users): array
    {
        return $this->makePostRequest('users', $users);
    }

    public function delete(string $id): void
    {
        $this->makeDeleteRequest("users/{$id}");
    }

    public function changeRole(string $id, array $roleData): array
    {
        return $this->makePatchRequest("users/{$id}/role", $roleData);
    }
}
