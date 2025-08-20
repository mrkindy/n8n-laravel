<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use MrKindy\N8NLaravel\Jobs\QueuedOperation;
use MrKindy\N8NLaravel\Services\Strategies\SyncExecutionStrategy;
use MrKindy\N8NLaravel\Services\Strategies\AsyncExecutionStrategy;
use MrKindy\N8NLaravel\Services\Strategies\QueuedExecutionStrategy;

it('sync strategy executes immediately', function () {
    $strategy = new SyncExecutionStrategy();
    
    $result = $strategy->execute(function () {
        return 'executed';
    });
    
    expect($result)->toBe('executed')
        ->and($strategy->getName())->toBe('sync');
});

it('queued strategy returns queued message', function () {
    Queue::fake();
    
    $strategy = new QueuedExecutionStrategy();
    
    $result = $strategy->execute(function () {
        return 'executed';
    });
    
    expect($result)->toHaveKey('queued', true)
        ->and($result)->toHaveKey('message')
        ->and($strategy->getName())->toBe('queued');
        
    Queue::assertPushed(QueuedOperation::class);
});

it('async strategy can be instantiated', function () {
    $strategy = new AsyncExecutionStrategy();
    expect($strategy->getName())->toBe('async');
});
