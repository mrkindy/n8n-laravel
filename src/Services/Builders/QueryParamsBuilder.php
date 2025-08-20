<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Builders;

use MrKindy\N8NLaravel\Contracts\BuilderInterface;

class QueryParamsBuilder implements BuilderInterface
{
    private array $params = [];

    public static function make(): static
    {
        return new static();
    }

    public function limit(int $limit): static
    {
        $this->params['limit'] = $limit;

        return $this;
    }

    public function cursor(string $cursor): static
    {
        $this->params['cursor'] = $cursor;

        return $this;
    }

    public function includeData(bool $include = true): static
    {
        $this->params['includeData'] = $include ? "true" : "false";

        return $this;
    }

    public function status(string $status): static
    {
        $this->params['status'] = $status;

        return $this;
    }

    public function workflowId(string $workflowId): static
    {
        $this->params['workflowId'] = $workflowId;

        return $this;
    }

    public function projectId(string $projectId): static
    {
        $this->params['projectId'] = $projectId;

        return $this;
    }

    public function active(bool $active): static
    {
        $this->params['active'] = $active ? "true" : "false";

        return $this;
    }

    public function tags(array $tags): static
    {
        $this->params['tags'] = implode(',', $tags);

        return $this;
    }

    public function name(string $name): static
    {
        $this->params['name'] = $name;

        return $this;
    }

    public function excludePinnedData(bool $exclude = true): static
    {
        $this->params['excludePinnedData'] = $exclude ? "true" : "false";

        return $this;
    }

    public function includeRole(bool $include = true): static
    {
        $this->params['includeRole'] = $include ? "true" : "false";

        return $this;
    }

    public function withParam(string $key, mixed $value): static
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function build(): array
    {
        return array_filter($this->params, fn($value) => $value !== null);
    }

    public function reset(): static
    {
        $this->params = [];

        return $this;
    }
}
