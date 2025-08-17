<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Builders;

use MrKindy\N8NLaravel\Contracts\BuilderInterface;

class CredentialPayloadBuilder implements BuilderInterface
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

    public function type(string $type): static
    {
        $this->data['type'] = $type;
        
        return $this;
    }

    public function data(array $data): static
    {
        $this->data['data'] = $data;
        
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
