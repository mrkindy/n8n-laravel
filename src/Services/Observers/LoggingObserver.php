<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Observers;

use Illuminate\Support\Facades\Log;
use MrKindy\N8NLaravel\Contracts\ObserverInterface;

class LoggingObserver implements ObserverInterface
{
    public function __construct(
        private readonly string $channel = 'default'
    ) {
    }

    public function onRequestSent(array $requestData): void
    {
        Log::channel($this->channel)->info('N8N Request Sent', [
            'method' => $requestData['method'],
            'url' => $requestData['url'],
            'payload_size' => count($requestData['payload']),
        ]);
    }

    public function onResponseReceived(array $responseData): void
    {
        Log::channel($this->channel)->info('N8N Response Received', [
            'method' => $responseData['method'],
            'url' => $responseData['url'],
            'status_code' => $responseData['statusCode'],
            'duration' => $responseData['duration'],
        ]);
    }

    public function onRequestFailed(array $errorData): void
    {
        Log::channel($this->channel)->error('N8N Request Failed', [
            'method' => $errorData['method'],
            'url' => $errorData['url'],
            'exception' => $errorData['exception']->getMessage(),
        ]);
    }
}
