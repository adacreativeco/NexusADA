<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProviderInterface
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $this->model = config('services.openai.model', env('OPENAI_MODEL', 'gpt-4o-mini'));
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getProviderName(): string
    {
        return 'openai';
    }

    public function chat(string $prompt, string $systemPrompt = '', array $options = []): string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException("OpenAI API key not configured.");
        }

        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::withToken($this->apiKey)->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content', '');
        }

        Log::error("OpenAI Error: " . $response->body());
        throw new \RuntimeException("OpenAI call failed: " . $response->status());
    }

    public function generateEmbedding(string $text): array
    {
        if ($this->isAvailable()) {
            $res = Http::withToken($this->apiKey)->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ]);
            if ($res->successful()) {
                return $res->json('data.0.embedding', []);
            }
        }
        return array_map(fn($v) => (float)($v / 255.0), array_slice(unpack('C*', md5($text, true)), 0, 16));
    }
}
