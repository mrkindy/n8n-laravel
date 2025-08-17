<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Exceptions;

class N8NApiException extends N8NException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly array $responseData,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }
}
