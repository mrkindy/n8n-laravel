<?php

declare(strict_types=1);

use MrKindy\N8NLaravel\Services\Observers\MetricsObserver;
use MrKindy\N8NLaravel\Services\Observers\LoggingObserver;

it('metrics observer tracks requests correctly', function () {
    $observer = new MetricsObserver();

    // Initial state
    $metrics = $observer->getMetrics();
    expect($metrics['requests_sent'])->toBe(0)
        ->and($metrics['responses_received'])->toBe(0)
        ->and($metrics['requests_failed'])->toBe(0);

    // Track a request
    $observer->onRequestSent(['method' => 'GET', 'url' => 'test']);
    $metrics = $observer->getMetrics();
    expect($metrics['requests_sent'])->toBe(1);

    // Track a successful response
    $observer->onResponseReceived([
        'method' => 'GET',
        'url' => 'test',
        'statusCode' => 200,
        'duration' => 1.5
    ]);
    $metrics = $observer->getMetrics();
    expect($metrics['responses_received'])->toBe(1)
        ->and($metrics['total_duration'])->toBe(1.5)
        ->and($metrics['average_duration'])->toBe(1.5);

    // Track a failed request
    $observer->onRequestFailed(['method' => 'POST', 'url' => 'test']);
    $metrics = $observer->getMetrics();
    expect($metrics['requests_failed'])->toBe(1);
});

it('metrics observer calculates average duration correctly', function () {
    $observer = new MetricsObserver();

    $observer->onResponseReceived(['duration' => 1.0]);
    $observer->onResponseReceived(['duration' => 2.0]);
    $observer->onResponseReceived(['duration' => 3.0]);

    $metrics = $observer->getMetrics();
    expect($metrics['responses_received'])->toBe(3)
        ->and($metrics['total_duration'])->toBe(6.0)
        ->and($metrics['average_duration'])->toBe(2.0);
});

it('metrics observer can reset metrics', function () {
    $observer = new MetricsObserver();

    $observer->onRequestSent(['method' => 'GET']);
    $observer->onResponseReceived(['duration' => 1.0]);
    $observer->onRequestFailed(['method' => 'POST']);

    $observer->resetMetrics();
    $metrics = $observer->getMetrics();
    
    expect($metrics['requests_sent'])->toBe(0)
        ->and($metrics['responses_received'])->toBe(0)
        ->and($metrics['requests_failed'])->toBe(0)
        ->and($metrics['total_duration'])->toBe(0.0)
        ->and($metrics['average_duration'])->toBe(0.0);
});

it('logging observer can be instantiated', function () {
    $observer = new LoggingObserver();
    expect($observer)->toBeInstanceOf(LoggingObserver::class);
    
    $observer = new LoggingObserver('custom-channel');
    expect($observer)->toBeInstanceOf(LoggingObserver::class);
});
