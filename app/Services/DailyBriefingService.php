<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Event;
use App\Models\Contract;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Activity;
use Illuminate\Support\Facades\Cache;

class DailyBriefingService
{
    /**
     * Get or generate daily briefing for tenant
     */
    public static function getBriefing(int $tenantId, bool $forceRefresh = false): string
    {
        $cacheKey = 'daily_briefing_tenant_' . $tenantId;

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($tenantId) {
            return self::generate($tenantId);
        });
    }

    /**
     * Generate the daily briefing from live database data using AI
     */
    protected static function generate(int $tenantId): string
    {
        // 1. Overdue Tasks
        $overdueTasksCount = Task::where('tenant_id', $tenantId)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        // 2. Today's Events
        $eventsTodayCount = Event::where('tenant_id', $tenantId)
            ->whereDate('start_date', now()->toDateString())
            ->count();

        // 3. Expiring Contracts
        $expiringContractsCount = Contract::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        // 4. Financial Summary
        $tryBalance = BankAccount::where('tenant_id', $tenantId)->where('currency', 'TRY')->sum('balance');
        $usdBalance = BankAccount::where('tenant_id', $tenantId)->where('currency', 'USD')->sum('balance');

        // 5. Pending approvals count
        $pendingApprovalsCount = Expense::where('tenant_id', $tenantId)->where('status', 'pending_approval')->count();

        // Compile data payload
        $dataText = "ADA Co-OS Günlük Operasyonel Veri Raporu:\n";
        $dataText .= "- Geciken Görev Sayısı: {$overdueTasksCount}\n";
        $dataText .= "- Bugün Planlanan Toplantı: {$eventsTodayCount}\n";
        $dataText .= "- 30 Gün İçinde Bitecek Sözleşme: {$expiringContractsCount}\n";
        $dataText .= "- Toplam TL Likit Bakiye: ₺" . number_format($tryBalance, 2, ',', '.') . "\n";
        $dataText .= "- Toplam USD Likit Bakiye: $" . number_format($usdBalance, 2, ',', '.') . "\n";
        $dataText .= "- Onay Bekleyen Gider Talebi: {$pendingApprovalsCount}\n";

        $hour = now()->hour;
        $isEvening = ($hour >= 18 || $hour < 5);
        $briefingType = $isEvening ? "Günü Kapatırken (Günün Değerlendirmesi / Akşam Brifingi)" : "Güne Başlarken (Sabah Brifingi)";
        $introText = $isEvening ? "Günü kapatırken ADA Co-OS sizin için tüm operasyonu ve günün özetini çıkardı:" : "Güne başlarken ADA Co-OS sizin için tüm operasyonu analiz etti:";

        $systemPrompt = "Sen ADA Co-OS akıllı iş asistanısın. Yönetici için {$briefingType} hazırlıyorsun. " .
            "Raporu okuyan yöneticiye operasyon ve finans durumunu net, motivasyonel, profesyonel ve son derece kısa maddeler halinde Türkçe özetle. " .
            "Giriş kısmına veya madde başlarına asla yabancı dillerde karakterler (özellikle Çince veya Japonca karakterler olan '以下' vb.) veya alakasız semboller ekleme. " .
            "Çıktının tamamı akıcı, temiz ve profesyonel bir Türkçe olmalıdır.";

        return AIService::ask($dataText, $systemPrompt, 'briefing', $tenantId);
    }
}
