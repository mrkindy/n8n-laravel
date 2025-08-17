<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\AuditServiceInterface;

class AuditService extends BaseService implements AuditServiceInterface
{
    public function generate(array $options = []): array
    {
        return $this->makePostRequest('audit', $options);
    }
}
