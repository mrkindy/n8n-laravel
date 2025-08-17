<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use MrKindy\N8NLaravel\Contracts\AdapterInterface;

/**
 * @method static \MrKindy\N8NLaravel\Contracts\WorkflowServiceInterface workflows()
 * @method static \MrKindy\N8NLaravel\Contracts\CredentialServiceInterface credentials()
 * @method static \MrKindy\N8NLaravel\Contracts\ExecutionServiceInterface executions()
 * @method static \MrKindy\N8NLaravel\Contracts\UserServiceInterface users()
 * @method static \MrKindy\N8NLaravel\Contracts\TagServiceInterface tags()
 * @method static \MrKindy\N8NLaravel\Contracts\VariableServiceInterface variables()
 * @method static \MrKindy\N8NLaravel\Contracts\ProjectServiceInterface projects()
 * @method static \MrKindy\N8NLaravel\Contracts\AuditServiceInterface audit()
 * @method static \MrKindy\N8NLaravel\Contracts\SourceControlServiceInterface sourceControl()
 * 
 * @see \MrKindy\N8NLaravel\Services\N8NAdapter
 */
class N8N extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AdapterInterface::class;
    }
}
