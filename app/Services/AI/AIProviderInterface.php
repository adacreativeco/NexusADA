<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    public function chat(string $prompt, string $systemPrompt = '', array $options = []): string;
    public function generateEmbedding(string $text): array;
    public function isAvailable(): bool;
    public function getProviderName(): string;
}
