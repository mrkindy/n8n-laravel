<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Builders;

use MrKindy\N8NLaravel\Contracts\BuilderInterface;

class WorkflowPayloadBuilder implements BuilderInterface
{
    private array $data = [];

    public static function make(): static
    {
        return new static();
    }

    public function name(string $name): static
    {
        $this->data['name'] = $name;
        
        return $this;
    }

    public function active(bool $active = true): static
    {
        $this->data['active'] = $active;
        
        return $this;
    }

    public function nodes(array $nodes): static
    {
        $this->data['nodes'] = $nodes;
        
        return $this;
    }

    public function connections(array $connections): static
    {
        $this->data['connections'] = $connections;
        
        return $this;
    }

    public function settings(array $settings): static
    {
        $this->data['settings'] = $settings;
        
        return $this;
    }

    public function tags(array $tags): static
    {
        $this->data['tags'] = $tags;
        
        return $this;
    }

    public function withParam(string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        
        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }

    public function reset(): static
    {
        $this->data = [];
        
        return $this;
    }
}
