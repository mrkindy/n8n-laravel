<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use MrKindy\N8NLaravel\Contracts\SourceControlServiceInterface;

class SourceControlService extends BaseService implements SourceControlServiceInterface
{
    public function pull(array $options = []): array
    {
        return $this->makePostRequest('source-control/pull', $options);
    }
}
