<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaProvider implements AIProviderInterface
{
    protected string $host;
    protected string $model;

    public function __construct()
    {
        $this->host = env('OLLAMA_HOST', 'http://127.0.0.1:11434');
        $this->model = env('OLLAMA_MODEL', 'llama3.2');
    }

    public function isAvailable(): bool
    {
        try {
            return Http::timeout(1)->get("{$this->host}/api/tags")->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProviderName(): string
    {
        return 'ollama';
    }

    public function chat(string $prompt, string $systemPrompt = '', array $options = []): string
    {
        $response = Http::timeout(60)->post("{$this->host}/api/generate", [
            'model' => $options['model'] ?? $this->model,
            'system' => $systemPrompt,
            'prompt' => $prompt,
            'stream' => false,
        ]);

        if ($response->successful()) {
            return $response->json('response', '');
        }

        Log::error("Ollama Error: " . $response->body());
        throw new \RuntimeException("Ollama call failed.");
    }

    public function generateEmbedding(string $text): array
    {
        return array_map(fn($v) => (float)($v / 255.0), array_slice(unpack('C*', md5($text, true)), 0, 16));
    }
}
