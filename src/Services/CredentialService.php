<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\CredentialServiceInterface;

class CredentialService extends BaseService implements CredentialServiceInterface
{
    public function create(array $data): array
    {
        return $this->makePostRequest('credentials', $data);
    }

    public function delete(string $id): array
    {
        return $this->makeDeleteRequest("credentials/{$id}");
    }

    public function getSchema(string $credentialTypeName): array
    {
        return $this->makeGetRequest("credentials/schema/{$credentialTypeName}");
    }

    public function transfer(string $id, string $destinationProjectId): array
    {
        return $this->makePutRequest("credentials/{$id}/transfer", [
            'destinationProjectId' => $destinationProjectId,
        ]);
    }
}
