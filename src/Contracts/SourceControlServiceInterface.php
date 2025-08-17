<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface SourceControlServiceInterface
{
    public function pull(array $options = []): array;
}
