<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicProvider implements AIProviderInterface
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', env('ANTHROPIC_API_KEY'));
        $this->model = config('services.anthropic.model', env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'));
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getProviderName(): string
    {
        return 'anthropic';
    }

    public function chat(string $prompt, string $systemPrompt = '', array $options = []): string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException("Anthropic API key not configured.");
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => $options['model'] ?? $this->model,
            'system' => $systemPrompt,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => $options['max_tokens'] ?? 2048,
        ]);

        if ($response->successful()) {
            return $response->json('content.0.text', '');
        }

        Log::error("Anthropic Error: " . $response->body());
        throw new \RuntimeException("Anthropic call failed: " . $response->status());
    }

    public function generateEmbedding(string $text): array
    {
        return array_map(fn($v) => (float)($v / 255.0), array_slice(unpack('C*', md5($text, true)), 0, 16));
    }
}
