<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services\Observers;

use MrKindy\N8NLaravel\Contracts\ObserverInterface;

class MetricsObserver implements ObserverInterface
{
    private array $metrics = [
        'requests_sent' => 0,
        'responses_received' => 0,
        'requests_failed' => 0,
        'total_duration' => 0.0,
        'average_duration' => 0.0,
    ];

    public function onRequestSent(array $requestData): void
    {
        $this->metrics['requests_sent']++;
    }

    public function onResponseReceived(array $responseData): void
    {
        $this->metrics['responses_received']++;
        $this->metrics['total_duration'] += $responseData['duration'];
        $this->calculateAverageDuration();
    }

    public function onRequestFailed(array $errorData): void
    {
        $this->metrics['requests_failed']++;
    }

    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function resetMetrics(): void
    {
        $this->metrics = [
            'requests_sent' => 0,
            'responses_received' => 0,
            'requests_failed' => 0,
            'total_duration' => 0.0,
            'average_duration' => 0.0,
        ];
    }

    private function calculateAverageDuration(): void
    {
        if ($this->metrics['responses_received'] > 0) {
            $this->metrics['average_duration'] = $this->metrics['total_duration'] / $this->metrics['responses_received'];
        }
    }
}
