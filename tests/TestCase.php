<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use MrKindy\N8NLaravel\N8NServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            N8NServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'N8N' => \MrKindy\N8NLaravel\Facades\N8N::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('n8n.base_url', 'http://localhost:5678');
        $app['config']->set('n8n.api_key', 'test-api-key');
        $app['config']->set('n8n.events.enabled', false);
        $app['config']->set('n8n.logging.enabled', false);
    }
}
