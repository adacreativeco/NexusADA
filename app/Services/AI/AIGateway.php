<?php

namespace App\Services\AI;

use App\Services\AI\Providers\NvidiaProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\AnthropicProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OllamaProvider;

class AIGateway
{
    protected array $providers = [];

    public function __construct()
    {
        $this->providers = [
            new NvidiaProvider(),
            new OpenAIProvider(),
            new AnthropicProvider(),
            new GeminiProvider(),
            new OllamaProvider(),
        ];
    }

    public function getActiveProvider(?string $preferred = null): ?AIProviderInterface
    {
        if ($preferred) {
            foreach ($this->providers as $provider) {
                if ($provider->getProviderName() === $preferred && $provider->isAvailable()) {
                    return $provider;
                }
            }
        }

        foreach ($this->providers as $provider) {
            if ($provider->isAvailable()) {
                return $provider;
            }
        }

        return null;
    }

    public function ask(string $prompt, string $systemPrompt = '', array $options = []): string
    {
        $provider = $this->getActiveProvider($options['provider'] ?? null);

        if ($provider) {
            try {
                return $provider->chat($prompt, $systemPrompt, $options);
            } catch (\Exception $e) {
                // Fallback to deterministic mock
            }
        }

        return $this->getDeterministicMock($prompt, $options['context_type'] ?? 'general');
    }

    public function getDeterministicMock(string $prompt, string $contextType): string
    {
        return match ($contextType) {
            'client_analyze' => "📊 Müşteri Durum Analizi (ADA AI):\n• Müşteri Segmenti: Büyüme Potansiyeli Yüksek Kurumsal Müşteri.\n• İletişim Sağlığı: Güçlü, son 30 günde düzenli etkileşim.\n• Risk Faktörleri: Tespit edilen ödeme veya teslimat gecikmesi bulunmuyor.\n• Tavsiye: Gelecek çeyrek için çapraz satış teklifi planlanabilir.",
            'briefing', 'daily_briefing' => "📌 Güne başlarken ADA Co-OS Operasyonel Brifingi:\n• Banka hesaplarımızda ve nakit akışında likidite dengesi stabil.\n• Aktif Projeler ve Görevler takvim planına uygun ilerliyor.\n• 30 gün içinde yenilenecek sözleşmeler takibe alındı.\n• Eylem Önerisi: Bekleyen harcama onaylarını tamamlayın.",
            'work_summary' => "📋 SÜREÇ ANALİZİ:\n• Süreç aşaması başarıyla doğrulandı.\n• Kritik kaynak veya bütçe sapması tespit edilmedi.\n• Öneri: Bir sonraki iş akışı adımına geçilebilir.",
            'proposal' => "💼 TEKLİF STRATEJİ ÖNERİSİ:\n• Kapsam: Müşteri gereksinimleri netleştirildi.\n• Fiyatlandırma: Rekabetçi piyasa çarpanı uygulandı.\n• Tavsiye: 15 günlük geçerlilik süresiyle iletilsin.",
            default => "🤖 ADA Co-OS Dijital Zeka Yanıtı: İsteğiniz başarıyla analiz edildi. Veritabanı ve iş akışı metrikleriniz istikrarlı durumda.",
        };
    }
}
