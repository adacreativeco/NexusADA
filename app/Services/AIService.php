<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\AIMemory;
use App\Services\AI\AIGateway;
use App\Services\AI\VectorStore;
use Illuminate\Support\Facades\Log;

class AIService
{
    public static function ask(string $prompt, string $systemPrompt = '', string $contextType = 'general', ?int $tenantId = null): string
    {
        // 1. Enrich prompt with RAG semantic memory if tenantId is provided
        if ($tenantId) {
            $relevant = VectorStore::search($tenantId, $prompt, 3);
            if (!empty($relevant)) {
                $ragContext = "\n\n[BAĞLAMSAL KURUMSAL HAFIZA]:\n";
                foreach ($relevant as $r) {
                    $ragContext .= "• " . $r['memory']->key . ": " . $r['memory']->content . "\n";
                }
                $systemPrompt .= $ragContext;
            }
        }

        // 2. Route through Multi-LLM AIGateway
        $gateway = new AIGateway();
        $response = $gateway->ask($prompt, $systemPrompt, ['context_type' => $contextType]);

        // 3. Log AI usage
        try {
            if ($tenantId) {
                AiUsageLog::create([
                    'tenant_id' => $tenantId,
                    'user_id' => auth()->id() ?? 1,
                    'action' => $contextType,
                    'prompt' => $prompt,
                    'response' => $response,
                    'tokens_used' => (int)(strlen($prompt . $response) / 4),
                ]);
            }
        } catch (\Exception $e) {
            // Ignore logging error in unit test sandbox
        }

        return $response;
    }
}
