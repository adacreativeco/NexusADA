<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NvidiaProvider implements AIProviderInterface
{
    protected ?string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.nvidia.api_key', env('NVIDIA_API_KEY'));
        $this->model = config('services.nvidia.model', env('NVIDIA_MODEL', 'meta/llama-3.1-70b-instruct'));
        $this->baseUrl = config('services.nvidia.base_url', env('NVIDIA_BASE_URL', 'https://integrate.api.nvidia.com/v1'));
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getProviderName(): string
    {
        return 'nvidia';
    }

    public function chat(string $prompt, string $systemPrompt = '', array $options = []): string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException("NVIDIA API key not configured.");
        }

        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 2048,
        ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content', '');
        }

        Log::error("Nvidia AI Error: " . $response->body());
        throw new \RuntimeException("NVIDIA AI call failed: " . $response->status());
    }

    public function generateEmbedding(string $text): array
    {
        return array_map(fn($v) => (float)($v / 255.0), array_slice(unpack('C*', md5($text, true)), 0, 16));
    }
}
