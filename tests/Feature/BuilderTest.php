<?php

declare(strict_types=1);

use MrKindy\N8NLaravel\Services\Builders\WorkflowPayloadBuilder;
use MrKindy\N8NLaravel\Services\Builders\CredentialPayloadBuilder;
use MrKindy\N8NLaravel\Services\Builders\QueryParamsBuilder;

it('can build workflow payload', function () {
    $payload = WorkflowPayloadBuilder::make()
        ->name('Test Workflow')
        ->active(true)
        ->nodes([
            ['id' => 'node1', 'type' => 'n8n-nodes-base.start'],
            ['id' => 'node2', 'type' => 'n8n-nodes-base.httpRequest']
        ])
        ->connections(['node1' => ['node2']])
        ->settings(['saveManualExecutions' => true])
        ->tags(['production', 'api'])
        ->withParam('description', 'A test workflow')
        ->build();

    expect($payload)->toHaveKey('name', 'Test Workflow')
        ->and($payload)->toHaveKey('active', true)
        ->and($payload)->toHaveKey('nodes')
        ->and($payload)->toHaveKey('connections')
        ->and($payload)->toHaveKey('settings')
        ->and($payload)->toHaveKey('tags')
        ->and($payload)->toHaveKey('description', 'A test workflow');
});

it('can build credential payload', function () {
    $payload = CredentialPayloadBuilder::make()
        ->name('Test Credential')
        ->type('httpBasicAuth')
        ->data([
            'username' => 'testuser',
            'password' => 'testpass'
        ])
        ->withParam('shared', true)
        ->build();

    expect($payload)->toHaveKey('name', 'Test Credential')
        ->and($payload)->toHaveKey('type', 'httpBasicAuth')
        ->and($payload)->toHaveKey('data')
        ->and($payload)->toHaveKey('shared', true);
});

it('can build query parameters', function () {
    $params = QueryParamsBuilder::make()
        ->limit(10)
        ->cursor('next-page-cursor')
        ->includeData(true)
        ->status('success')
        ->workflowId('workflow-123')
        ->projectId('project-456')
        ->active(true)
        ->tags(['tag1', 'tag2'])
        ->name('Test Workflow')
        ->excludePinnedData(true)
        ->includeRole(true)
        ->withParam('custom', 'value')
        ->build();

    expect($params)->toHaveKey('limit', 10)
        ->and($params)->toHaveKey('cursor', 'next-page-cursor')
        ->and($params)->toHaveKey('includeData', true)
        ->and($params)->toHaveKey('status', 'success')
        ->and($params)->toHaveKey('workflowId', 'workflow-123')
        ->and($params)->toHaveKey('projectId', 'project-456')
        ->and($params)->toHaveKey('active', true)
        ->and($params)->toHaveKey('tags', 'tag1,tag2')
        ->and($params)->toHaveKey('name', 'Test Workflow')
        ->and($params)->toHaveKey('excludePinnedData', true)
        ->and($params)->toHaveKey('includeRole', true)
        ->and($params)->toHaveKey('custom', 'value');
});

it('can reset builder state', function () {
    $builder = WorkflowPayloadBuilder::make()
        ->name('Test')
        ->active(true);

    $firstBuild = $builder->build();
    expect($firstBuild)->toHaveKey('name', 'Test');

    $secondBuild = $builder->reset()->build();
    expect($secondBuild)->toBeEmpty();
});
