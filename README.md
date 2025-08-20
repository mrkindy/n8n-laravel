# Laravel n8n Package

A comprehensive Laravel package for integrating with the n8n API using clean architecture patterns. This package provides a facade-based interface with support for multiple execution strategies, request builders, and event observability.

![Alpha](https://img.shields.io/badge/status-alpha-red?style=for-the-badge)

> ⚠️ **Warning**:  
> This package is currently in **beta stage**.

## Features

- **Facade** - Simple static interface (`N8N::workflows()`)
- **Adapter** - Clean abstraction of n8n API communication
- **Strategy** - Multiple execution strategies (sync, async, queued)
- **Builder** - Fluent API for constructing complex payloads
- **Observer** - Event hooks for request lifecycle
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

For detailed usage instructions, please refer to [DOCUMENTATION.md](DOCUMENTATION.md).

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
│   ├── BuilderTest.php        # Builder tests
│   ├── ObserverTest.php       # Observer tests
│   └── StrategyTest.php       # Strategy tests
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
