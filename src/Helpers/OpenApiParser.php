<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Helpers;

use Symfony\Component\Yaml\Yaml;
use MrKindy\N8NLaravel\Exceptions\N8NException;

class OpenApiParser
{
    private array $schema;

    public function __construct(string $schemaPath)
    {
        if (!file_exists($schemaPath)) {
            throw new N8NException("OpenAPI schema file not found: {$schemaPath}");
        }

        $this->schema = Yaml::parseFile($schemaPath);
    }

    public function getEndpoints(): array
    {
        return $this->schema['paths'] ?? [];
    }

    public function getEndpoint(string $path): ?array
    {
        return $this->schema['paths'][$path] ?? null;
    }

    public function getTags(): array
    {
        return $this->schema['tags'] ?? [];
    }

    public function getComponents(): array
    {
        return $this->schema['components'] ?? [];
    }

    public function getSchemas(): array
    {
        return $this->schema['components']['schemas'] ?? [];
    }

    public function getVersion(): string
    {
        return $this->schema['info']['version'] ?? '1.0.0';
    }

    public function getBaseUrl(): string
    {
        $servers = $this->schema['servers'] ?? [];
        
        return $servers[0]['url'] ?? '/api/v1';
    }

    public function getEndpointsByTag(string $tag): array
    {
        $endpoints = [];
        
        foreach ($this->getEndpoints() as $path => $methods) {
            foreach ($methods as $method => $config) {
                if (isset($config['tags']) && in_array($tag, $config['tags'])) {
                    $endpoints[$path][$method] = $config;
                }
            }
        }
        
        return $endpoints;
    }
}
