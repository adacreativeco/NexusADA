<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->model = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.0-flash'));
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    public function chat(string $prompt, string $systemPrompt = '', array $options = []): string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException("Gemini API key not configured.");
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        
        $contents = [];
        if (!empty($systemPrompt)) {
            $contents[] = ['role' => 'user', 'parts' => [['text' => "System Instruction: " . $systemPrompt]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];

        $response = Http::timeout(30)->post($url, ['contents' => $contents]);

        if ($response->successful()) {
            return $response->json('candidates.0.content.parts.0.text', '');
        }

        Log::error("Gemini Error: " . $response->body());
        throw new \RuntimeException("Gemini call failed: " . $response->status());
    }

    public function generateEmbedding(string $text): array
    {
        return array_map(fn($v) => (float)($v / 255.0), array_slice(unpack('C*', md5($text, true)), 0, 16));
    }
}
