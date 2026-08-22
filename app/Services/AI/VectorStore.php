<?php

namespace App\Services\AI;

use App\Models\AIMemory;

class VectorStore
{
    /**
     * Store an embedding vector for a memory record
     */
    public static function store(int $tenantId, string $key, string $content, string $category = 'general', ?array $metadata = []): AIMemory
    {
        $gateway = new AIGateway();
        $provider = $gateway->getActiveProvider();
        $embedding = $provider ? $provider->generateEmbedding($content) : self::mockEmbedding($content);

        return AIMemory::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            [
                'content' => $content,
                'category' => $category,
                'metadata' => array_merge($metadata ?? [], ['embedding' => $embedding]),
            ]
        );
    }

    /**
     * Search tenant memories by cosine similarity
     */
    public static function search(int $tenantId, string $query, int $limit = 5): array
    {
        $gateway = new AIGateway();
        $provider = $gateway->getActiveProvider();
        $queryEmbedding = $provider ? $provider->generateEmbedding($query) : self::mockEmbedding($query);

        $memories = AIMemory::where('tenant_id', $tenantId)->get();
        $results = [];

        foreach ($memories as $memory) {
            $vec = $memory->metadata['embedding'] ?? null;
            if ($vec && is_array($vec)) {
                $score = self::cosineSimilarity($queryEmbedding, $vec);
                $results[] = [
                    'memory' => $memory,
                    'score' => $score,
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, $limit);
    }

    public static function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $len = min(count($vecA), count($vecB));

        for ($i = 0; $i < $len; $i++) {
            $dot += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] * $vecA[$i];
            $normB += $vecB[$i] * $vecB[$i];
        }

        $denom = sqrt($normA) * sqrt($normB);
        return $denom > 0.00001 ? ($dot / $denom) : 0.0;
    }

    public static function mockEmbedding(string $text): array
    {
        return array_map(fn($v) => (float)($v / 255.0), array_slice(unpack('C*', md5($text, true)), 0, 16));
    }
}
