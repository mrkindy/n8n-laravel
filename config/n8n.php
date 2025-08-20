<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | N8N Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of your n8n instance. This should include the protocol
    | (http or https) and the domain/IP address.
    |
    */
    'base_url' => env('N8N_BASE_URL', 'http://localhost:5678'),

    /*
    |--------------------------------------------------------------------------
    | N8N API Key
    |--------------------------------------------------------------------------
    |
    | The API key for authenticating with your n8n instance.
    | You can generate this in your n8n instance settings.
    |
    */
    'api_key' => env('N8N_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Execution Strategy
    |--------------------------------------------------------------------------
    |
    | The default strategy to use for API calls. Available options:
    | - sync: Synchronous execution (default)
    | - async: Asynchronous execution
    | - queued: Queue-based execution
    |
    */
    'default_strategy' => env('N8N_DEFAULT_STRATEGY', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the HTTP client used to make API requests.
    |
    */
    'http' => [
        'timeout' => (int) env('N8N_HTTP_TIMEOUT', 30),
        'retry' => [
            'times' => (int) env('N8N_HTTP_RETRY_TIMES', 3),
            'sleep' => (int) env('N8N_HTTP_RETRY_SLEEP', 1000),
        ],
        'verify' => (bool) env('N8N_HTTP_VERIFY_SSL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for queued execution strategy.
    |
    */
    'queue' => [
        'connection' => env('N8N_QUEUE_CONNECTION', 'default'),
        'queue' => env('N8N_QUEUE_NAME', 'n8n'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Events Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable event dispatching for API operations.
    |
    */
    'events' => [
        'enabled' => env('N8N_EVENTS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for n8n operations.
    |
    */
    'logging' => [
        'enabled' => env('N8N_LOGGING_ENABLED', true),
        'level' => env('N8N_LOGGING_LEVEL', 'info'),
        'channel' => env('N8N_LOGGING_CHANNEL', 'default'),
    ],
];
