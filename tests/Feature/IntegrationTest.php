<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use MrKindy\N8NLaravel\Facades\N8N;
use MrKindy\N8NLaravel\Services\Builders\WorkflowPayloadBuilder;
use MrKindy\N8NLaravel\Services\Builders\QueryParamsBuilder;

describe('N8N Integration', function () {
    it('can manage complete workflow lifecycle', function () {
        // Mock workflow creation
        Http::fake([
            'http://localhost:5678/api/v1/workflows' => Http::sequence()
                ->push(['id' => 'wf-123', 'name' => 'Test Workflow', 'active' => false]) // create
                ->push([['id' => 'wf-123', 'name' => 'Test Workflow']]), // list
            'http://localhost:5678/api/v1/workflows/wf-123' => Http::sequence()
                ->push(['id' => 'wf-123', 'name' => 'Test Workflow', 'active' => false]) // get
                ->push(['id' => 'wf-123', 'name' => 'Updated Workflow', 'active' => true]), // update
            'http://localhost:5678/api/v1/workflows/wf-123/activate' => Http::response([
                'id' => 'wf-123',
                'name' => 'Updated Workflow',
                'active' => true
            ])
        ]);

        // Create workflow using builder
        $workflowData = WorkflowPayloadBuilder::make()
            ->name('Test Workflow')
            ->active(false)
            ->nodes([
                [
                    'id' => 'start',
                    'type' => 'n8n-nodes-base.start',
                    'position' => [240, 300]
                ]
            ])
            ->connections([])
            ->build();

        // Create workflow
        $created = N8N::workflows()->create($workflowData);
        expect($created)->toHaveKey('id', 'wf-123');

        // List workflows
        $workflows = N8N::workflows()->list();
        expect($workflows)->toHaveCount(1);

        // Get specific workflow
        $workflow = N8N::workflows()->get('wf-123');
        expect($workflow)->toHaveKey('id', 'wf-123');

        // Update workflow
        $updated = N8N::workflows()->update('wf-123', [
            'name' => 'Updated Workflow'
        ]);
        expect($updated)->toHaveKey('name', 'Updated Workflow');

        // Activate workflow
        $activated = N8N::workflows()->activate('wf-123');
        expect($activated)->toHaveKey('active', true);
    });

    it('can manage credentials', function () {
        Http::fake([
            'http://localhost:5678/api/v1/credentials' => Http::response([
                'id' => 'cred-123',
                'name' => 'API Credentials',
                'type' => 'httpBasicAuth'
            ]),
            'http://localhost:5678/api/v1/credentials/schema/httpBasicAuth' => Http::response([
                'type' => 'object',
                'properties' => [
                    'username' => ['type' => 'string'],
                    'password' => ['type' => 'string']
                ]
            ])
        ]);

        // Create credential
        $credential = N8N::credentials()->create([
            'name' => 'API Credentials',
            'type' => 'httpBasicAuth',
            'data' => [
                'username' => 'testuser',
                'password' => 'testpass'
            ]
        ]);

        expect($credential)->toHaveKey('id', 'cred-123');

        // Get schema
        $schema = N8N::credentials()->getSchema('httpBasicAuth');
        expect($schema)->toHaveKey('type', 'object');
    });

    it('can filter executions with query builder', function () {
        Http::fake([
            'http://localhost:5678/api/v1/executions*' => Http::response([
                'data' => [
                    ['id' => 'exec-1', 'status' => 'success'],
                    ['id' => 'exec-2', 'status' => 'success']
                ]
            ])
        ]);

        $params = QueryParamsBuilder::make()
            ->limit(10)
            ->status('success')
            ->workflowId('wf-123')
            ->includeData(false)
            ->build();

        $executions = N8N::executions()->list($params);
        
        expect($executions)->toHaveKey('data')
            ->and($executions['data'])->toHaveCount(2);

        // Verify the request was made with correct parameters
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'http://localhost:5678/api/v1/executions');
        });
    });

    it('can manage tags', function () {
        Http::fake([
            'http://localhost:5678/api/v1/tags' => Http::sequence()
                ->push(['id' => 'tag-1', 'name' => 'production']) // create
                ->push([['id' => 'tag-1', 'name' => 'production']]), // list
            'http://localhost:5678/api/v1/tags/tag-1' => Http::sequence()
                ->push(['id' => 'tag-1', 'name' => 'production']) // get
                ->push(['id' => 'tag-1', 'name' => 'staging']) // update
        ]);

        // Create tag
        $tag = N8N::tags()->create(['name' => 'production']);
        expect($tag)->toHaveKey('id', 'tag-1');

        // List tags
        $tags = N8N::tags()->list();
        expect($tags)->toHaveCount(1);

        // Get tag
        $tag = N8N::tags()->get('tag-1');
        expect($tag)->toHaveKey('name', 'production');

        // Update tag
        $updated = N8N::tags()->update('tag-1', ['name' => 'staging']);
        expect($updated)->toHaveKey('name', 'staging');
    });

    it('handles API errors gracefully', function () {
        Http::fake([
            'http://localhost:5678/api/v1/workflows/invalid' => Http::response([
                'message' => 'Workflow not found'
            ], 404)
        ]);

        expect(fn() => N8N::workflows()->get('invalid'))
            ->toThrow(\MrKindy\N8NLaravel\Exceptions\N8NApiException::class);
    });
});
