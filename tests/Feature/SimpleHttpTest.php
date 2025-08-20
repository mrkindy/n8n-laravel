<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use MrKindy\N8NLaravel\Facades\N8N;

describe('Simple HTTP Test', function () {
    it('can use Http fake', function () {
        Http::fake([
            'http://localhost:5678/api/v1/workflows' => Http::response(['message' => 'fake response'])
        ]);

        try {
            $result = N8N::workflows()->list();
            expect($result)->toHaveKey('message', 'fake response');
        } catch (\Exception $e) {
            throw $e;
        }
    });
});
