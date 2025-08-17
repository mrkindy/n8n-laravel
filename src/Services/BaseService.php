<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

abstract class BaseService
{
    public function __construct(
        protected readonly N8NAdapter $adapter
    ) {
    }

    protected function makeGetRequest(string $endpoint, array $params = []): array
    {
        return $this->adapter->makeRequest('GET', $endpoint, $params);
    }

    protected function makePostRequest(string $endpoint, array $data = []): array
    {
        return $this->adapter->makeRequest('POST', $endpoint, $data);
    }

    protected function makePutRequest(string $endpoint, array $data = []): array
    {
        return $this->adapter->makeRequest('PUT', $endpoint, $data);
    }

    protected function makePatchRequest(string $endpoint, array $data = []): array
    {
        return $this->adapter->makeRequest('PATCH', $endpoint, $data);
    }

    protected function makeDeleteRequest(string $endpoint, array $data = []): array
    {
        return $this->adapter->makeRequest('DELETE', $endpoint, $data);
    }
}
