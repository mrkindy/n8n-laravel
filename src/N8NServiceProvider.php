<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel;

use Illuminate\Support\ServiceProvider;
use MrKindy\N8NLaravel\Contracts\AdapterInterface;
use MrKindy\N8NLaravel\Contracts\StrategyInterface;
use MrKindy\N8NLaravel\Services\N8NAdapter;
use MrKindy\N8NLaravel\Services\Strategies\SyncExecutionStrategy;

class N8NServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/n8n.php',
            'n8n'
        );

        $this->app->singleton(AdapterInterface::class, function ($app) {
            return new N8NAdapter(
                baseUrl: config('n8n.base_url'),
                apiKey: config('n8n.api_key'),
                httpConfig: config('n8n.http', []),
                eventsEnabled: config('n8n.events.enabled', true)
            );
        });

        $this->app->singleton(StrategyInterface::class, function ($app) {
            $strategy = config('n8n.default_strategy', 'sync');
            
            return match ($strategy) {
                'sync' => $app->make(SyncExecutionStrategy::class),
                'async' => $app->make(\MrKindy\N8NLaravel\Services\Strategies\AsyncExecutionStrategy::class),
                'queued' => $app->make(\MrKindy\N8NLaravel\Services\Strategies\QueuedExecutionStrategy::class),
                default => $app->make(SyncExecutionStrategy::class),
            };
        });

        $this->app->alias(AdapterInterface::class, 'n8n.adapter');
        $this->app->alias(StrategyInterface::class, 'n8n.strategy');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/n8n.php' => config_path('n8n.php'),
            ], 'n8n-config');

            $this->publishes([
                __DIR__ . '/../n8n-openapi.yml' => storage_path('app/n8n-openapi.yml'),
            ], 'n8n-schema');
        }
    }

    public function provides(): array
    {
        return [
            AdapterInterface::class,
            StrategyInterface::class,
            'n8n.adapter',
            'n8n.strategy',
        ];
    }
}
