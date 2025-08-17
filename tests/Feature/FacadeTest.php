<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use MrKindy\N8NLaravel\Facades\N8N;
use MrKindy\N8NLaravel\Services\N8NAdapter;

beforeEach(function () {
    $this->httpMock = Mockery::mock(Factory::class);
    $this->responseMock = Mockery::mock(Response::class);
});

it('can access workflows service through facade', function () {
    expect(N8N::workflows())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\WorkflowServiceInterface::class);
});

it('can access credentials service through facade', function () {
    expect(N8N::credentials())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\CredentialServiceInterface::class);
});

it('can access executions service through facade', function () {
    expect(N8N::executions())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\ExecutionServiceInterface::class);
});

it('can access users service through facade', function () {
    expect(N8N::users())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\UserServiceInterface::class);
});

it('can access tags service through facade', function () {
    expect(N8N::tags())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\TagServiceInterface::class);
});

it('can access variables service through facade', function () {
    expect(N8N::variables())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\VariableServiceInterface::class);
});

it('can access projects service through facade', function () {
    expect(N8N::projects())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\ProjectServiceInterface::class);
});

it('can access audit service through facade', function () {
    expect(N8N::audit())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\AuditServiceInterface::class);
});

it('can access source control service through facade', function () {
    expect(N8N::sourceControl())->toBeInstanceOf(\MrKindy\N8NLaravel\Contracts\SourceControlServiceInterface::class);
});

it('throws exception when base url is missing', function () {
    expect(fn() => new N8NAdapter('', 'test-key'))
        ->toThrow(\MrKindy\N8NLaravel\Exceptions\N8NConfigurationException::class);
});

it('throws exception when api key is missing', function () {
    expect(fn() => new N8NAdapter('http://localhost:5678', ''))
        ->toThrow(\MrKindy\N8NLaravel\Exceptions\N8NConfigurationException::class);
});
