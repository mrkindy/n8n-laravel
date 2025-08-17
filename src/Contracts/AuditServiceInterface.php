<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface AuditServiceInterface
{
    public function generate(array $options = []): array;
}
