<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface AdapterInterface
{
    public function workflows(): WorkflowServiceInterface;
    
    public function credentials(): CredentialServiceInterface;
    
    public function executions(): ExecutionServiceInterface;
    
    public function users(): UserServiceInterface;
    
    public function tags(): TagServiceInterface;
    
    public function variables(): VariableServiceInterface;
    
    public function projects(): ProjectServiceInterface;
    
    public function audit(): AuditServiceInterface;
    
    public function sourceControl(): SourceControlServiceInterface;
}
