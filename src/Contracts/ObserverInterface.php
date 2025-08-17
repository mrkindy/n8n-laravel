<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Contracts;

interface ObserverInterface
{
    public function onRequestSent(array $requestData): void;
    
    public function onResponseReceived(array $responseData): void;
    
    public function onRequestFailed(array $errorData): void;
}
