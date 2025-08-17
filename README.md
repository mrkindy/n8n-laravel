# Laravel n8n Package

A comprehensive Laravel package for integrating with the n8n API using clean architecture patterns. This package provides a facade-based interface with support for multiple execution strategies, request builders, and event observability.

## Features

- **Facade Pattern** - Simple static interface (`N8N::workflows()`)
- **Adapter Pattern** - Clean abstraction of n8n API communication
- **Strategy Pattern** - Multiple execution strategies (sync, async, queued)
- **Builder Pattern** - Fluent API for constructing complex payloads
- **Observer Pattern** - Event hooks for request lifecycle
- **Clean Architecture** - SOLID principles and separation of concerns
- **Type Safety** - Full PHP 8.1+ type hints and strict typing
- **Event System** - Laravel events for request monitoring
- **Comprehensive Testing** - Pest framework with full test coverage

## Installation

```bash
composer require mrkindy/n8n-laravel
```

### Publish Configuration

```bash
php artisan vendor:publish --provider="MrKindy\N8NLaravel\N8NServiceProvider" --tag="n8n-config"
```

### Environment Configuration

Add these variables to your `.env` file:

```env
N8N_BASE_URL=http://localhost:5678
N8N_API_KEY=your-api-key-here
N8N_DEFAULT_STRATEGY=sync
N8N_HTTP_TIMEOUT=30
N8N_HTTP_RETRY_TIMES=3
N8N_HTTP_RETRY_SLEEP=1000
N8N_HTTP_VERIFY_SSL=true
N8N_EVENTS_ENABLED=true
N8N_LOGGING_ENABLED=true
N8N_LOGGING_LEVEL=info
N8N_LOGGING_CHANNEL=default
N8N_QUEUE_CONNECTION=default
N8N_QUEUE_NAME=n8n
```

## Usage

### Facade Interface

The package provides a simple facade interface for all n8n operations:

```php
use MrKindy\N8NLaravel\Facades\N8N;

// Workflows
$workflows = N8N::workflows()->list();
$workflow = N8N::workflows()->get('workflow-id');
$created = N8N::workflows()->create($workflowData);
N8N::workflows()->activate('workflow-id');
N8N::workflows()->deactivate('workflow-id');

// Credentials
$credential = N8N::credentials()->create($credentialData);
$schema = N8N::credentials()->getSchema('httpBasicAuth');

// Executions
$executions = N8N::executions()->list(['status' => 'success']);
$execution = N8N::executions()->get('execution-id');

// Users (Enterprise features)
$users = N8N::users()->list();
$user = N8N::users()->create($userData);

// Tags
$tags = N8N::tags()->list();
$tag = N8N::tags()->create(['name' => 'production']);

// Variables
$variables = N8N::variables()->list();
$variable = N8N::variables()->create(['key' => 'API_URL', 'value' => 'https://api.example.com']);

// Projects (Enterprise features)
$projects = N8N::projects()->list();

// Audit
$audit = N8N::audit()->generate();

// Source Control
$result = N8N::sourceControl()->pull();
```

### Builder Pattern

Use builders to construct complex request payloads:

```php
use MrKindy\N8NLaravel\Services\Builders\WorkflowPayloadBuilder;
use MrKindy\N8NLaravel\Services\Builders\CredentialPayloadBuilder;
use MrKindy\N8NLaravel\Services\Builders\QueryParamsBuilder;

// Workflow creation with builder
$workflow = WorkflowPayloadBuilder::make()
    ->name('My API Workflow')
    ->active(true)
    ->nodes([
        [
            'id' => 'start-node',
            'type' => 'n8n-nodes-base.start',
            'position' => [240, 300]
        ],
        [
            'id' => 'http-node',
            'type' => 'n8n-nodes-base.httpRequest',
            'position' => [460, 300],
            'parameters' => [
                'url' => 'https://api.example.com/data',
                'method' => 'GET'
            ]
        ]
    ])
    ->connections([
        'start-node' => [['http-node']]
    ])
    ->settings([
        'saveManualExecutions' => true,
        'callerPolicy' => 'workflowsFromSameOwner'
    ])
    ->tags(['production', 'api'])
    ->withParam('description', 'Fetches data from external API')
    ->build();

$created = N8N::workflows()->create($workflow);

// Credential creation with builder
$credential = CredentialPayloadBuilder::make()
    ->name('API Credentials')
    ->type('httpBasicAuth')
    ->data([
        'username' => 'api_user',
        'password' => 'secure_password'
    ])
    ->build();

$created = N8N::credentials()->create($credential);

// Query parameters with builder
$params = QueryParamsBuilder::make()
    ->limit(50)
    ->active(true)
    ->tags(['production', 'api'])
    ->excludePinnedData(true)
    ->build();

$workflows = N8N::workflows()->list($params);
```

### Execution Strategies

Choose different execution strategies based on your needs:

#### Synchronous (Default)
```php
// Executes immediately and returns result
$result = N8N::workflows()->list();
```

#### Asynchronous
```php
// Configure async strategy in config/n8n.php
'default_strategy' => 'async',

// Or use dependency injection
app()->singleton(\MrKindy\N8NLaravel\Contracts\StrategyInterface::class, function() {
    return new \MrKindy\N8NLaravel\Services\Strategies\AsyncExecutionStrategy();
});
```

#### Queued
```php
// Configure queued strategy in config/n8n.php
'default_strategy' => 'queued',

// Operations will be dispatched to Laravel queues
$result = N8N::workflows()->list(); // Returns: ['queued' => true, 'message' => '...']
```

### Observer Pattern

Attach observers to monitor API requests:

```php
use MrKindy\N8NLaravel\Services\Observers\LoggingObserver;
use MrKindy\N8NLaravel\Services\Observers\MetricsObserver;

$adapter = app(\MrKindy\N8NLaravel\Contracts\AdapterInterface::class);

// Add logging observer
$adapter->addObserver(new LoggingObserver('n8n-channel'));

// Add metrics observer
$metricsObserver = new MetricsObserver();
$adapter->addObserver($metricsObserver);

// Make requests...
N8N::workflows()->list();

// Get metrics
$metrics = $metricsObserver->getMetrics();
/*
[
    'requests_sent' => 1,
    'responses_received' => 1,
    'requests_failed' => 0,
    'total_duration' => 0.250,
    'average_duration' => 0.250
]
*/
```

### Custom Observers

Create custom observers by implementing the `ObserverInterface`:

```php
use MrKindy\N8NLaravel\Contracts\ObserverInterface;

class CustomObserver implements ObserverInterface
{
    public function onRequestSent(array $requestData): void
    {
        // Handle request sent event
        logger('N8N request sent', $requestData);
    }

    public function onResponseReceived(array $responseData): void
    {
        // Handle response received event
        if ($responseData['statusCode'] >= 400) {
            // Alert on errors
        }
    }

    public function onRequestFailed(array $errorData): void
    {
        // Handle request failed event
        report($errorData['exception']);
    }
}

$adapter->addObserver(new CustomObserver());
```

### Events

The package dispatches Laravel events for monitoring:

```php
use MrKindy\N8NLaravel\Events\N8NRequestSent;
use MrKindy\N8NLaravel\Events\N8NResponseReceived;
use MrKindy\N8NLaravel\Events\N8NRequestFailed;

// Listen to events in EventServiceProvider
protected $listen = [
    N8NRequestSent::class => [
        'App\Listeners\LogN8NRequest',
    ],
    N8NResponseReceived::class => [
        'App\Listeners\LogN8NResponse',
    ],
    N8NRequestFailed::class => [
        'App\Listeners\AlertN8NFailure',
    ],
];
```

### Error Handling

The package provides structured exception handling:

```php
use MrKindy\N8NLaravel\Exceptions\N8NApiException;
use MrKindy\N8NLaravel\Exceptions\N8NConfigurationException;

try {
    $workflow = N8N::workflows()->get('invalid-id');
} catch (N8NApiException $e) {
    $statusCode = $e->statusCode;        // HTTP status code
    $responseData = $e->responseData;    // API response data
    $message = $e->getMessage();         // Error message
} catch (N8NConfigurationException $e) {
    // Handle configuration errors
}
```

## Testing

The package uses the Pest testing framework. Run tests with:

```bash
vendor/bin/pest
```

### Test Structure

```
tests/
├── Pest.php                    # Pest configuration
├── TestCase.php                # Base test case
├── Feature/
│   ├── FacadeTest.php         # Facade functionality tests
│   ├── BuilderTest.php        # Builder pattern tests
│   ├── ObserverTest.php       # Observer pattern tests
│   └── StrategyTest.php       # Strategy pattern tests
└── Unit/
    ├── AdapterTest.php        # Adapter unit tests
    └── ServiceTest.php        # Individual service tests
```

### Writing Tests

```php
use MrKindy\N8NLaravel\Facades\N8N;

it('can create a workflow', function () {
    Http::fake([
        'localhost:5678/api/v1/workflows' => Http::response([
            'id' => 'workflow-123',
            'name' => 'Test Workflow'
        ])
    ]);

    $result = N8N::workflows()->create([
        'name' => 'Test Workflow',
        'nodes' => []
    ]);

    expect($result)->toHaveKey('id', 'workflow-123');
});
```

## Configuration Reference

### HTTP Client Options

```php
'http' => [
    'timeout' => 30,                    // Request timeout in seconds
    'retry' => [
        'times' => 3,                   // Number of retry attempts
        'sleep' => 1000,                // Sleep between retries (ms)
    ],
    'verify' => true,                   // SSL certificate verification
],
```

### Queue Configuration

```php
'queue' => [
    'connection' => 'redis',            // Queue connection
    'queue' => 'n8n-operations',       // Queue name
],
```

### Logging Configuration

```php
'logging' => [
    'enabled' => true,                  // Enable request logging
    'level' => 'info',                  // Log level
    'channel' => 'n8n',                // Log channel
],
```

## API Coverage

This package provides full coverage of the n8n Public API v1.1.1:

### Workflows
- ✅ List workflows
- ✅ Get workflow
- ✅ Create workflow
- ✅ Update workflow
- ✅ Delete workflow
- ✅ Activate workflow
- ✅ Deactivate workflow
- ✅ Transfer workflow
- ✅ Get workflow tags
- ✅ Update workflow tags

### Credentials
- ✅ Create credential
- ✅ Delete credential
- ✅ Get credential schema
- ✅ Transfer credential

### Executions
- ✅ List executions
- ✅ Get execution
- ✅ Delete execution

### Users (Enterprise)
- ✅ List users
- ✅ Get user
- ✅ Create users
- ✅ Delete user
- ✅ Change user role

### Tags
- ✅ List tags
- ✅ Get tag
- ✅ Create tag
- ✅ Update tag
- ✅ Delete tag

### Variables
- ✅ List variables
- ✅ Create variable
- ✅ Update variable
- ✅ Delete variable

### Projects (Enterprise)
- ✅ List projects
- ✅ Create project
- ✅ Delete project
- ✅ Add users to project

### Audit
- ✅ Generate audit

### Source Control
- ✅ Pull changes

## Contributing

1. Fork the repository
2. Create a feature branch
3. Write tests for your changes
4. Ensure all tests pass: `vendor/bin/pest`
5. Follow PSR-12 coding standards
6. Submit a pull request

### Development Setup

```bash
git clone https://github.com/mrkindy/n8n-laravel.git
cd n8n-laravel
composer install
cp .env.example .env
# Configure your n8n instance details in .env
vendor/bin/pest
```

## Requirements

- PHP 8.1+
- Laravel 10.0+ or 11.0+
- n8n instance with API access

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

- [Documentation](https://docs.n8n.io/api/)
- [Issues](https://github.com/mrkindy/n8n-laravel/issues)
- [Discussions](https://github.com/mrkindy/n8n-laravel/discussions)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

Built with ❤️ for the Laravel and n8n communities.
