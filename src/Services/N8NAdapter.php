<?php

declare(strict_types=1);

namespace MrKindy\N8NLaravel\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use MrKindy\N8NLaravel\Contracts\AdapterInterface;
use MrKindy\N8NLaravel\Contracts\AuditServiceInterface;
use MrKindy\N8NLaravel\Contracts\CredentialServiceInterface;
use MrKindy\N8NLaravel\Contracts\ExecutionServiceInterface;
use MrKindy\N8NLaravel\Contracts\ObserverInterface;
use MrKindy\N8NLaravel\Contracts\ProjectServiceInterface;
use MrKindy\N8NLaravel\Contracts\SourceControlServiceInterface;
use MrKindy\N8NLaravel\Contracts\TagServiceInterface;
use MrKindy\N8NLaravel\Contracts\UserServiceInterface;
use MrKindy\N8NLaravel\Contracts\VariableServiceInterface;
use MrKindy\N8NLaravel\Contracts\WorkflowServiceInterface;
use MrKindy\N8NLaravel\Events\N8NRequestFailed;
use MrKindy\N8NLaravel\Events\N8NRequestSent;
use MrKindy\N8NLaravel\Events\N8NResponseReceived;
use MrKindy\N8NLaravel\Exceptions\N8NApiException;
use MrKindy\N8NLaravel\Exceptions\N8NConfigurationException;

class N8NAdapter implements AdapterInterface
{
    private PendingRequest $httpClient;
    private array $observers = [];

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey,
        private readonly array $httpConfig = [],
        private readonly bool $eventsEnabled = true,
        ?HttpFactory $httpFactory = null
    ) {
        if (empty($this->baseUrl)) {
            throw new N8NConfigurationException('N8N base URL is required');
        }

        if (empty($this->apiKey)) {
            throw new N8NConfigurationException('N8N API key is required');
        }

        $factory = $httpFactory ?? new HttpFactory();
        
        $this->httpClient = $factory
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withToken($this->apiKey)
            ->timeout($this->httpConfig['timeout'] ?? 30)
            ->retry(
                $this->httpConfig['retry']['times'] ?? 3,
                $this->httpConfig['retry']['sleep'] ?? 1000
            );

        if (isset($this->httpConfig['verify'])) {
            $this->httpClient = $this->httpClient->withOptions([
                'verify' => $this->httpConfig['verify'],
            ]);
        }
    }

    public function workflows(): WorkflowServiceInterface
    {
        return new WorkflowService($this);
    }

    public function credentials(): CredentialServiceInterface
    {
        return new CredentialService($this);
    }

    public function executions(): ExecutionServiceInterface
    {
        return new ExecutionService($this);
    }

    public function users(): UserServiceInterface
    {
        return new UserService($this);
    }

    public function tags(): TagServiceInterface
    {
        return new TagService($this);
    }

    public function variables(): VariableServiceInterface
    {
        return new VariableService($this);
    }

    public function projects(): ProjectServiceInterface
    {
        return new ProjectService($this);
    }

    public function audit(): AuditServiceInterface
    {
        return new AuditService($this);
    }

    public function sourceControl(): SourceControlServiceInterface
    {
        return new SourceControlService($this);
    }

    public function addObserver(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function removeObserver(ObserverInterface $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($obs) => $obs !== $observer
        );
    }

    public function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);
        $startTime = microtime(true);
        $timestamp = time();

        $requestData = [
            'method' => $method,
            'url' => $url,
            'headers' => $this->httpClient->getOptions()['headers'] ?? [],
            'payload' => $data,
        ];

        $this->notifyObservers('onRequestSent', $requestData);

        if ($this->eventsEnabled) {
            Event::dispatch(new N8NRequestSent(
                $method,
                $url,
                $requestData['headers'],
                $data,
                $timestamp
            ));
        }

        try {
            $response = match (strtoupper($method)) {
                'GET' => $this->httpClient->get($url, $data),
                'POST' => $this->httpClient->post($url, $data),
                'PUT' => $this->httpClient->put($url, $data),
                'PATCH' => $this->httpClient->patch($url, $data),
                'DELETE' => $this->httpClient->delete($url, $data),
                default => throw new N8NApiException("Unsupported HTTP method: {$method}", 400, []),
            };

            $duration = microtime(true) - $startTime;
            $responseData = $this->handleResponse($response);

            $responseEventData = [
                'method' => $method,
                'url' => $url,
                'statusCode' => $response->status(),
                'headers' => $response->headers(),
                'response' => $responseData,
                'duration' => $duration,
            ];

            $this->notifyObservers('onResponseReceived', $responseEventData);

            if ($this->eventsEnabled) {
                Event::dispatch(new N8NResponseReceived(
                    $method,
                    $url,
                    $response->status(),
                    $response->headers(),
                    $responseData,
                    $duration,
                    $timestamp
                ));
            }

            if (config('n8n.logging.enabled', true)) {
                Log::channel(config('n8n.logging.channel', 'default'))
                    ->log(
                        config('n8n.logging.level', 'info'),
                        'N8N API Request',
                        [
                            'method' => $method,
                            'url' => $url,
                            'status' => $response->status(),
                            'duration' => $duration,
                        ]
                    );
            }

            return $responseData;
        } catch (\Throwable $e) {
            $errorData = [
                'method' => $method,
                'url' => $url,
                'headers' => $requestData['headers'],
                'payload' => $data,
                'exception' => $e,
            ];

            $this->notifyObservers('onRequestFailed', $errorData);

            if ($this->eventsEnabled) {
                Event::dispatch(new N8NRequestFailed(
                    $method,
                    $url,
                    $requestData['headers'],
                    $data,
                    $e,
                    $timestamp
                ));
            }

            throw $e;
        }
    }

    private function buildUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $endpoint = ltrim($endpoint, '/');
        
        return "{$baseUrl}/api/v1/{$endpoint}";
    }

    private function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $responseBody = $response->json() ?? [];
        
        throw new N8NApiException(
            $responseBody['message'] ?? 'API request failed',
            $response->status(),
            $responseBody
        );
    }

    private function notifyObservers(string $method, array $data): void
    {
        foreach ($this->observers as $observer) {
            if (method_exists($observer, $method)) {
                $observer->{$method}($data);
            }
        }
    }
}
