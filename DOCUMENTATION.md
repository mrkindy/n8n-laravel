# Laravel n8n Package Documentation

Complete documentation for the Laravel n8n integration package.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Architecture Overview](#architecture-overview)
- [Usage Guide](#usage-guide)
- [API Reference](#api-reference)
- [Design Patterns](#design-patterns)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Performance](#performance)
- [Security](#security)

## Installation

### Step 1: Install via Composer

```bash
composer require mrkindy/n8n-laravel
```

### Step 2: Publish Configuration Files

```bash
# Publish configuration
php artisan vendor:publish --provider="MrKindy\N8NLaravel\N8NServiceProvider" --tag="n8n-config"

# Publish OpenAPI schema (optional)
php artisan vendor:publish --provider="MrKindy\N8NLaravel\N8NServiceProvider" --tag="n8n-schema"
```

### Step 3: Configure Environment

Add the following to your `.env` file:

```env
# Required
N8N_BASE_URL=http://localhost:5678
N8N_API_KEY=your-api-key-here

# Optional - HTTP Configuration
N8N_DEFAULT_STRATEGY=sync
N8N_HTTP_TIMEOUT=30
N8N_HTTP_RETRY_TIMES=3
N8N_HTTP_RETRY_SLEEP=1000
N8N_HTTP_VERIFY_SSL=true

# Optional - Events & Logging
N8N_EVENTS_ENABLED=true
N8N_LOGGING_ENABLED=true
N8N_LOGGING_LEVEL=info
N8N_LOGGING_CHANNEL=default

# Optional - Queue Configuration
N8N_QUEUE_CONNECTION=default
N8N_QUEUE_NAME=n8n
```

## Configuration

### Basic Configuration

The package configuration is located in `config/n8n.php`. Here's the complete configuration breakdown:

```php
return [
    // Base URL of your n8n instance
    'base_url' => env('N8N_BASE_URL', 'http://localhost:5678'),
    
    // API key for authentication
    'api_key' => env('N8N_API_KEY'),
    
    // Default execution strategy
    'default_strategy' => env('N8N_DEFAULT_STRATEGY', 'sync'),
    
    // HTTP client configuration
    'http' => [
        'timeout' => env('N8N_HTTP_TIMEOUT', 30),
        'retry' => [
            'times' => env('N8N_HTTP_RETRY_TIMES', 3),
            'sleep' => env('N8N_HTTP_RETRY_SLEEP', 1000),
        ],
        'verify' => env('N8N_HTTP_VERIFY_SSL', true),
    ],
    
    // Queue configuration for queued strategy
    'queue' => [
        'connection' => env('N8N_QUEUE_CONNECTION', 'default'),
        'queue' => env('N8N_QUEUE_NAME', 'n8n'),
    ],
    
    // Event configuration
    'events' => [
        'enabled' => env('N8N_EVENTS_ENABLED', true),
    ],
    
    // Logging configuration
    'logging' => [
        'enabled' => env('N8N_LOGGING_ENABLED', true),
        'level' => env('N8N_LOGGING_LEVEL', 'info'),
        'channel' => env('N8N_LOGGING_CHANNEL', 'default'),
    ],
];
```

### Advanced Configuration

#### Custom HTTP Client

You can customize the HTTP client behavior:

```php
// In a service provider
$this->app->singleton(AdapterInterface::class, function ($app) {
    $httpFactory = new \Illuminate\Http\Client\Factory();
    
    return new N8NAdapter(
        baseUrl: config('n8n.base_url'),
        apiKey: config('n8n.api_key'),
        httpConfig: [
            'timeout' => 60,
            'retry' => ['times' => 5, 'sleep' => 2000],
            'verify' => false, // For self-signed certificates
        ],
        eventsEnabled: true,
        httpFactory: $httpFactory
    );
});
```

#### Custom Strategy Registration

```php
// In a service provider
$this->app->singleton(StrategyInterface::class, function ($app) {
    return match (config('n8n.default_strategy')) {
        'sync' => new SyncExecutionStrategy(),
        'async' => new AsyncExecutionStrategy(),
        'queued' => new QueuedExecutionStrategy(),
        'custom' => new YourCustomStrategy(),
        default => new SyncExecutionStrategy(),
    };
});
```

## Architecture Overview

### Design Patterns Used

1. **Facade Pattern** - Provides a simplified interface (`N8N::workflows()`)
2. **Adapter Pattern** - Abstracts n8n API communication
3. **Strategy Pattern** - Different execution strategies (sync, async, queued)
4. **Builder Pattern** - Fluent API for complex request construction
5. **Observer Pattern** - Event hooks for request lifecycle monitoring

### Package Structure

```
src/
├── N8NServiceProvider.php          # Laravel service provider
├── Facades/
│   └── N8N.php                     # Main facade
├── Contracts/                      # Interface definitions
│   ├── AdapterInterface.php
│   ├── StrategyInterface.php
│   ├── BuilderInterface.php
│   ├── ObserverInterface.php
│   └── *ServiceInterface.php       # Service contracts
├── Services/
│   ├── N8NAdapter.php              # Main adapter implementation
│   ├── BaseService.php             # Base service class
│   ├── *Service.php                # Individual API services
│   ├── Strategies/                 # Execution strategies
│   ├── Builders/                   # Request builders
│   └── Observers/                  # Built-in observers
├── Events/                         # Laravel events
├── Exceptions/                     # Custom exceptions
└── Helpers/                        # Utility classes
```

### Request Flow

```
Client Code → Facade → Adapter → Strategy → HTTP Client → n8n API
    ↑                    ↓
Events/Observers ← Response Handler
```

## Usage Guide

### Basic Usage

#### Workflows

```php
use MrKindy\N8NLaravel\Facades\N8N;

// List all workflows
$workflows = N8N::workflows()->list();

// List with filters
$workflows = N8N::workflows()->list([
    'active' => true,
    'tags' => 'production,api',
    'limit' => 10
]);

// Get specific workflow
$workflow = N8N::workflows()->get('workflow-id');

// Get workflow without pinned data
$workflow = N8N::workflows()->get('workflow-id', [
    'excludePinnedData' => true
]);

// Create workflow
$workflowData = [
    'name' => 'My Workflow',
    'active' => false,
    'nodes' => [
        [
            'id' => 'start',
            'type' => 'n8n-nodes-base.start',
            'position' => [240, 300]
        ]
    ],
    'connections' => []
];

$created = N8N::workflows()->create($workflowData);

// Update workflow
$updated = N8N::workflows()->update('workflow-id', $workflowData);

// Activate/Deactivate
N8N::workflows()->activate('workflow-id');
N8N::workflows()->deactivate('workflow-id');

// Manage tags
$tags = N8N::workflows()->getTags('workflow-id');
N8N::workflows()->updateTags('workflow-id', ['tag1', 'tag2']);

// Transfer to another project
N8N::workflows()->transfer('workflow-id', 'destination-project-id');

// Delete workflow
N8N::workflows()->delete('workflow-id');
```

#### Credentials

```php
// Create credential
$credentialData = [
    'name' => 'My API Key',
    'type' => 'httpHeaderAuth',
    'data' => [
        'name' => 'Authorization',
        'value' => 'Bearer your-token-here'
    ]
];

$credential = N8N::credentials()->create($credentialData);

// Get credential schema
$schema = N8N::credentials()->getSchema('httpBasicAuth');

// Transfer credential
N8N::credentials()->transfer('credential-id', 'destination-project-id');

// Delete credential
N8N::credentials()->delete('credential-id');
```

#### Executions

```php
// List executions
$executions = N8N::executions()->list();

// List with filters
$executions = N8N::executions()->list([
    'status' => 'success',
    'workflowId' => 'workflow-id',
    'limit' => 50
]);

// Get execution details
$execution = N8N::executions()->get('execution-id');

// Get execution with data
$execution = N8N::executions()->get('execution-id', [
    'includeData' => true
]);

// Delete execution
N8N::executions()->delete('execution-id');
```

### Advanced Usage with Builders

#### Workflow Builder

```php
use MrKindy\N8NLaravel\Services\Builders\WorkflowPayloadBuilder;

$workflow = WorkflowPayloadBuilder::make()
    ->name('Complex API Workflow')
    ->active(true)
    ->nodes([
        [
            'id' => 'webhook',
            'type' => 'n8n-nodes-base.webhook',
            'position' => [240, 300],
            'parameters' => [
                'path' => 'webhook-endpoint',
                'httpMethod' => 'POST'
            ]
        ],
        [
            'id' => 'http',
            'type' => 'n8n-nodes-base.httpRequest',
            'position' => [460, 300],
            'parameters' => [
                'url' => 'https://api.example.com/process',
                'method' => 'POST',
                'sendBody' => true,
                'bodyContentType' => 'json'
            ]
        ],
        [
            'id' => 'response',
            'type' => 'n8n-nodes-base.respondToWebhook',
            'position' => [680, 300],
            'parameters' => [
                'statusCode' => 200
            ]
        ]
    ])
    ->connections([
        'webhook' => [
            [
                ['node' => 'http', 'type' => 'main', 'index' => 0]
            ]
        ],
        'http' => [
            [
                ['node' => 'response', 'type' => 'main', 'index' => 0]
            ]
        ]
    ])
    ->settings([
        'saveManualExecutions' => true,
        'callerPolicy' => 'workflowsFromSameOwner',
        'errorWorkflow' => 'error-handler-workflow-id'
    ])
    ->tags(['production', 'api', 'webhook'])
    ->withParam('description', 'Webhook that processes API requests')
    ->withParam('meta', ['category' => 'integration'])
    ->build();

$created = N8N::workflows()->create($workflow);
```

#### Query Parameters Builder

```php
use MrKindy\N8NLaravel\Services\Builders\QueryParamsBuilder;

$params = QueryParamsBuilder::make()
    ->limit(25)
    ->cursor('next-page-token')
    ->active(true)
    ->tags(['production', 'critical'])
    ->projectId('my-project-id')
    ->excludePinnedData(true)
    ->withParam('sortBy', 'updatedAt')
    ->withParam('sortOrder', 'desc')
    ->build();

$workflows = N8N::workflows()->list($params);
```

### Execution Strategies

#### Synchronous Execution (Default)

```php
// Immediate execution, blocks until response
$workflows = N8N::workflows()->list(); // Returns actual data
```

#### Asynchronous Execution

```php
// Configure in config/n8n.php
'default_strategy' => 'async',

// Or manually
app()->singleton(StrategyInterface::class, AsyncExecutionStrategy::class);

$promise = N8N::workflows()->list(); // Returns Promise
$workflows = $promise->wait(); // Wait for completion
```

#### Queued Execution

```php
// Configure in config/n8n.php
'default_strategy' => 'queued',

// Or manually
app()->singleton(StrategyInterface::class, QueuedExecutionStrategy::class);

$result = N8N::workflows()->list();
// Returns: ['queued' => true, 'message' => 'Operation queued for execution']

// The actual API call will be processed by Laravel queue workers
```

### Observer Pattern Implementation

#### Built-in Observers

```php
use MrKindy\N8NLaravel\Services\Observers\LoggingObserver;
use MrKindy\N8NLaravel\Services\Observers\MetricsObserver;

$adapter = app(AdapterInterface::class);

// Logging observer
$loggingObserver = new LoggingObserver('n8n-requests');
$adapter->addObserver($loggingObserver);

// Metrics observer
$metricsObserver = new MetricsObserver();
$adapter->addObserver($metricsObserver);

// Make some requests
N8N::workflows()->list();
N8N::executions()->list();

// Check metrics
$metrics = $metricsObserver->getMetrics();
// Output: [
//     'requests_sent' => 2,
//     'responses_received' => 2,
//     'requests_failed' => 0,
//     'total_duration' => 0.450,
//     'average_duration' => 0.225
// ]
```

#### Custom Observer

```php
use MrKindy\N8NLaravel\Contracts\ObserverInterface;

class SlackNotificationObserver implements ObserverInterface
{
    public function onRequestSent(array $requestData): void
    {
        // Optional: Log request initiation
    }

    public function onResponseReceived(array $responseData): void
    {
        if ($responseData['statusCode'] >= 400) {
            $this->notifySlack("n8n API error: {$responseData['statusCode']}");
        }
    }

    public function onRequestFailed(array $errorData): void
    {
        $this->notifySlack("n8n API request failed: {$errorData['exception']->getMessage()}");
    }

    private function notifySlack(string $message): void
    {
        // Slack notification logic
    }
}

$adapter->addObserver(new SlackNotificationObserver());
```

## API Reference

### Workflows Service

```php
N8N::workflows()->list(array $params = []): array
N8N::workflows()->get(string $id, array $params = []): array
N8N::workflows()->create(array $data): array
N8N::workflows()->update(string $id, array $data): array
N8N::workflows()->delete(string $id): array
N8N::workflows()->activate(string $id): array
N8N::workflows()->deactivate(string $id): array
N8N::workflows()->transfer(string $id, string $destinationProjectId): array
N8N::workflows()->getTags(string $id): array
N8N::workflows()->updateTags(string $id, array $tagIds): array
```

### Credentials Service

```php
N8N::credentials()->create(array $data): array
N8N::credentials()->delete(string $id): array
N8N::credentials()->getSchema(string $credentialTypeName): array
N8N::credentials()->transfer(string $id, string $destinationProjectId): array
```

### Executions Service

```php
N8N::executions()->list(array $params = []): array
N8N::executions()->get(string $id, array $params = []): array
N8N::executions()->delete(string $id): array
```

### Users Service (Enterprise)

```php
N8N::users()->list(array $params = []): array
N8N::users()->get(string $id, array $params = []): array
N8N::users()->create(array $users): array
N8N::users()->delete(string $id): void
N8N::users()->changeRole(string $id, array $roleData): array
```

### Tags Service

```php
N8N::tags()->list(array $params = []): array
N8N::tags()->get(string $id): array
N8N::tags()->create(array $data): array
N8N::tags()->update(string $id, array $data): array
N8N::tags()->delete(string $id): array
```

### Variables Service

```php
N8N::variables()->list(array $params = []): array
N8N::variables()->create(array $data): array
N8N::variables()->update(string $id, array $data): array
N8N::variables()->delete(string $id): void
```

### Projects Service (Enterprise)

```php
N8N::projects()->list(): array
N8N::projects()->create(array $data): array
N8N::projects()->delete(string $projectId): void
N8N::projects()->addUsers(string $projectId, array $users): array
```

### Audit Service

```php
N8N::audit()->generate(array $options = []): array
```

### Source Control Service

```php
N8N::sourceControl()->pull(array $options = []): array
```

## Design Patterns

### Facade Pattern

The facade provides a simple, unified interface:

```php
// Instead of:
$adapter = new N8NAdapter($url, $key);
$workflowService = new WorkflowService($adapter);
$workflows = $workflowService->list();

// Use:
$workflows = N8N::workflows()->list();
```

### Adapter Pattern

The adapter abstracts HTTP communication:

```php
interface AdapterInterface
{
    public function workflows(): WorkflowServiceInterface;
    public function makeRequest(string $method, string $endpoint, array $data = []): array;
}
```

### Strategy Pattern

Different execution strategies:

```php
interface StrategyInterface
{
    public function execute(Closure $operation): mixed;
    public function getName(): string;
}

// Usage
$strategy->execute(fn() => $httpClient->get('/workflows'));
```

### Builder Pattern

Fluent API for complex construction:

```php
$workflow = WorkflowPayloadBuilder::make()
    ->name('Test')
    ->active(true)
    ->nodes($nodes)
    ->build();
```

### Observer Pattern

Event monitoring:

```php
interface ObserverInterface
{
    public function onRequestSent(array $requestData): void;
    public function onResponseReceived(array $responseData): void;
    public function onRequestFailed(array $errorData): void;
}
```

## Testing

### Running Tests

```bash
# Run all tests
vendor/bin/pest

# Run specific test file
vendor/bin/pest tests/Feature/FacadeTest.php

# Run with coverage
vendor/bin/pest --coverage

# Run with specific filter
vendor/bin/pest --filter="can build workflow payload"
```

### Test Structure

```
tests/
├── Pest.php                    # Pest configuration
├── TestCase.php                # Base test case with Laravel setup
├── Feature/                    # Integration tests
│   ├── FacadeTest.php         # Facade pattern tests
│   ├── BuilderTest.php        # Builder pattern tests
│   ├── ObserverTest.php       # Observer pattern tests
│   └── StrategyTest.php       # Strategy pattern tests
└── Unit/                      # Unit tests
    ├── AdapterTest.php        # Adapter tests
    └── ServiceTest.php        # Service layer tests
```

### Writing Tests

```php
// Feature test example
it('can create workflow with builder', function () {
    Http::fake([
        'localhost:5678/api/v1/workflows' => Http::response([
            'id' => 'wf-123',
            'name' => 'Test Workflow'
        ])
    ]);

    $workflow = WorkflowPayloadBuilder::make()
        ->name('Test Workflow')
        ->active(true)
        ->build();

    $result = N8N::workflows()->create($workflow);

    expect($result)
        ->toHaveKey('id', 'wf-123')
        ->toHaveKey('name', 'Test Workflow');
});

// Unit test example
it('metrics observer tracks correctly', function () {
    $observer = new MetricsObserver();
    
    $observer->onRequestSent(['method' => 'GET']);
    $observer->onResponseReceived(['duration' => 1.5]);
    
    $metrics = $observer->getMetrics();
    expect($metrics['requests_sent'])->toBe(1)
        ->and($metrics['responses_received'])->toBe(1)
        ->and($metrics['average_duration'])->toBe(1.5);
});
```

### Mocking HTTP Requests

```php
use Illuminate\Support\Facades\Http;

// Mock successful response
Http::fake([
    'localhost:5678/api/v1/workflows' => Http::response([
        ['id' => 'wf-1', 'name' => 'Workflow 1'],
        ['id' => 'wf-2', 'name' => 'Workflow 2']
    ])
]);

// Mock error response
Http::fake([
    'localhost:5678/api/v1/workflows/*' => Http::response([
        'message' => 'Workflow not found'
    ], 404)
]);

// Mock with callback
Http::fake(function ($request) {
    if ($request->url() === 'localhost:5678/api/v1/workflows') {
        return Http::response(['workflows' => []]);
    }
    return Http::response(['error' => 'Not found'], 404);
});
```

## Troubleshooting

### Common Issues

#### SSL Certificate Issues

```env
# Disable SSL verification (development only)
N8N_HTTP_VERIFY_SSL=false
```

#### Timeout Issues

```env
# Increase timeout
N8N_HTTP_TIMEOUT=60

# Increase retry attempts
N8N_HTTP_RETRY_TIMES=5
N8N_HTTP_RETRY_SLEEP=2000
```

#### Authentication Issues

```bash
# Verify API key in n8n
curl -H "Authorization: Bearer YOUR_API_KEY" http://localhost:5678/api/v1/workflows

# Check configuration
php artisan config:cache
php artisan config:clear
```

#### Queue Issues

```bash
# Ensure queue worker is running
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Debug Mode

Enable debugging in your service provider:

```php
$this->app->singleton(AdapterInterface::class, function ($app) {
    return new N8NAdapter(
        baseUrl: config('n8n.base_url'),
        apiKey: config('n8n.api_key'),
        httpConfig: [
            'debug' => true, // Enable HTTP debug
        ],
        eventsEnabled: true
    );
});
```

### Logging Configuration

```php
// config/logging.php
'channels' => [
    'n8n' => [
        'driver' => 'single',
        'path' => storage_path('logs/n8n.log'),
        'level' => 'debug',
    ],
],

// config/n8n.php
'logging' => [
    'enabled' => true,
    'channel' => 'n8n',
    'level' => 'debug',
],
```

## Performance

### Optimization Tips

1. **Use Query Parameters Builder** for efficient filtering
2. **Enable HTTP Keep-Alive** for multiple requests
3. **Use Async Strategy** for non-blocking operations
4. **Implement Caching** for frequently accessed data
5. **Monitor with Observers** for performance insights

### Caching Example

```php
use Illuminate\Support\Facades\Cache;

// Cache workflow list
$workflows = Cache::remember('n8n.workflows', 300, function () {
    return N8N::workflows()->list();
});

// Cache with tags
Cache::tags(['n8n', 'workflows'])->put('workflow.123', $workflow, 600);

// Invalidate cache
Cache::tags(['n8n'])->flush();
```

### Connection Pooling

```php
// Configure in service provider
$this->app->singleton(AdapterInterface::class, function ($app) {
    $httpFactory = new \Illuminate\Http\Client\Factory();
    
    return new N8NAdapter(
        baseUrl: config('n8n.base_url'),
        apiKey: config('n8n.api_key'),
        httpConfig: [
            'pool_size' => 10,
            'max_connections' => 50,
        ],
        httpFactory: $httpFactory
    );
});
```

## Security

### Best Practices

1. **Secure API Keys**: Use Laravel's environment encryption
2. **Rate Limiting**: Implement rate limiting for API calls
3. **Input Validation**: Validate all input data
4. **Error Handling**: Don't expose sensitive information in errors
5. **HTTPS Only**: Always use HTTPS in production

### Environment Encryption

```bash
# Encrypt environment file
php artisan env:encrypt

# Use encrypted environment
php artisan env:decrypt
```

### Rate Limiting

```php
// In a middleware or service
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::attempt(
    'n8n-api:' . auth()->id(),
    $perMinute = 60,
    function () {
        return N8N::workflows()->list();
    }
);
```

### Input Validation

```php
// Use Laravel's validation
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'active' => 'boolean',
    'nodes' => 'array',
    'connections' => 'array',
]);

$workflow = N8N::workflows()->create($validated);
```

### Error Sanitization

```php
try {
    $result = N8N::workflows()->get($id);
} catch (N8NApiException $e) {
    // Log full error
    Log::error('N8N API Error', [
        'status' => $e->statusCode,
        'response' => $e->responseData,
        'user' => auth()->id(),
    ]);
    
    // Return sanitized error to client
    return response()->json([
        'error' => 'Unable to retrieve workflow'
    ], 500);
}
```

---

This documentation covers all aspects of the Laravel n8n package. For additional support, please refer to the GitHub repository or create an issue for specific problems.
