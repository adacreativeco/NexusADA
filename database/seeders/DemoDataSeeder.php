<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Department;
use App\Models\Event;
use App\Models\MediaInsight;
use App\Models\Project;
use App\Models\SocialPost;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = \App\Models\Tenant::first()?->id;
        $userId   = \App\Models\User::where('tenant_id', $tenantId)->first()?->id;

        if (!$tenantId || !$userId) {
            $this->command->error('Tenant veya User bulunamadı. Önce tenant/user oluşturun.');
            return;
        }

        // ── Departments ────────────────────────────
        $depts = [];
        foreach (['Pazarlama', 'Kurumsal İletişim', 'Dijital', 'Medya', 'Kreatif'] as $name) {
            $depts[$name] = Department::firstOrCreate(
                ['name' => $name, 'tenant_id' => $tenantId],
                ['name' => $name, 'tenant_id' => $tenantId]
            )->id;
        }

        // ── Clients ────────────────────────────────
        $clients = [
            ['name' => 'Yıldız Holding', 'industry' => 'FMCG', 'strategic_importance' => 5, 'decision_style' => 'Analitik', 'risk_level' => 'Düşük', 'behavioral_notes' => 'Veri odaklı, revizyon sever.'],
            ['name' => 'Getir Teknoloji', 'industry' => 'Teknoloji', 'strategic_importance' => 4, 'decision_style' => 'Hızlı', 'risk_level' => 'Orta', 'behavioral_notes' => 'Hızlı karar alır, deadline hassas.'],
            ['name' => 'İstanbul Büyükşehir Belediyesi', 'industry' => 'Kamu', 'strategic_importance' => 5, 'decision_style' => 'Konsensüs', 'risk_level' => 'Düşük', 'behavioral_notes' => 'Protokol önemli, sunum kalitesi kritik.'],
            ['name' => 'Karaca Home', 'industry' => 'Perakende', 'strategic_importance' => 3, 'decision_style' => 'Vizyoner', 'risk_level' => 'Düşük', 'behavioral_notes' => 'Marka imajı odaklı, premium içerik ister.'],
            ['name' => 'TechStar Yazılım', 'industry' => 'SaaS', 'strategic_importance' => 4, 'decision_style' => 'Teknik', 'risk_level' => 'Orta', 'behavioral_notes' => 'Teknik detaylara hakim, ölçülebilir sonuç ister.'],
        ];

        $clientIds = [];
        foreach ($clients as $c) {
            $client = Client::updateOrCreate(
                ['name' => $c['name'], 'tenant_id' => $tenantId],
                array_merge($c, ['tenant_id' => $tenantId])
            );
            $clientIds[] = $client->id;
        }

        // ── Projects ───────────────────────────────
        $projects = [
            ['client_name' => 'Yıldız Holding', 'summary' => '2026 Q2 dijital kampanya yönetimi ve sosyal medya stratejisi', 'status' => 'active', 'is_referenceable' => true, 'actual_revenue' => 185000, 'profitability_score' => 78, 'planned_hours' => 320, 'client_id' => $clientIds[0], 'completion_date' => now()->addDays(45)->format('Y-m-d')],
            ['client_name' => 'Getir Teknoloji', 'summary' => 'Uygulama lansmanı için 360° iletişim planı', 'status' => 'active', 'is_referenceable' => true, 'actual_revenue' => 120000, 'profitability_score' => 85, 'planned_hours' => 200, 'client_id' => $clientIds[1], 'completion_date' => now()->addDays(30)->format('Y-m-d')],
            ['client_name' => 'İBB', 'summary' => 'Kültür festivali etkinlik yönetimi ve basın organizasyonu', 'status' => 'active', 'is_referenceable' => false, 'actual_revenue' => 95000, 'profitability_score' => 62, 'planned_hours' => 280, 'client_id' => $clientIds[2], 'completion_date' => now()->addDays(60)->format('Y-m-d')],
            ['client_name' => 'Karaca Home', 'summary' => 'Yeni sezon marka kampanyası ve influencer yönetimi', 'status' => 'active', 'is_referenceable' => true, 'actual_revenue' => 75000, 'profitability_score' => 71, 'planned_hours' => 150, 'client_id' => $clientIds[3], 'completion_date' => now()->addDays(20)->format('Y-m-d')],
            ['client_name' => 'TechStar Yazılım', 'summary' => 'B2B lansman ve lead generation kampanyası', 'status' => 'active', 'is_referenceable' => true, 'actual_revenue' => 55000, 'profitability_score' => 90, 'planned_hours' => 120, 'client_id' => $clientIds[4], 'completion_date' => now()->addDays(15)->format('Y-m-d')],
            ['client_name' => 'Yıldız Holding', 'summary' => 'Q1 Kurumsal İletişim Denetimi (tamamlandı)', 'status' => 'completed', 'is_referenceable' => true, 'actual_revenue' => 210000, 'profitability_score' => 82, 'planned_hours' => 400, 'client_id' => $clientIds[0], 'completion_date' => now()->subDays(15)->format('Y-m-d')],
        ];

        $projectIds = [];
        foreach ($projects as $p) {
            $proj = Project::updateOrCreate(
                ['client_name' => $p['client_name'], 'summary' => $p['summary'], 'tenant_id' => $tenantId],
                array_merge($p, ['tenant_id' => $tenantId, 'created_at' => now()->subMonths(rand(1, 4))])
            );
            $projectIds[] = $proj->id;
        }

        // ── Tasks ──────────────────────────────────
        $tasks = [
            ['title' => 'Sosyal medya takvimi hazırla', 'status' => 'done', 'priority' => 'high', 'project_id' => $projectIds[0], 'start_date' => now()->subDays(10), 'due_date' => now()->subDays(3), 'completed_at' => now()->subDays(4)],
            ['title' => 'Instagram Reels senaryo onayı', 'status' => 'in_progress', 'priority' => 'high', 'project_id' => $projectIds[0], 'start_date' => now()->subDays(2), 'due_date' => now()->addDays(2)],
            ['title' => 'Getir lansman basın bülteni', 'status' => 'review', 'priority' => 'urgent', 'project_id' => $projectIds[1], 'start_date' => now()->subDays(3), 'due_date' => now()->addDays(1)],
            ['title' => 'Landing page A/B testi analizi', 'status' => 'todo', 'priority' => 'medium', 'project_id' => $projectIds[1], 'start_date' => now()->addDays(1), 'due_date' => now()->addDays(5)],
            ['title' => 'Festival sahne planı revizyonu', 'status' => 'in_progress', 'priority' => 'high', 'project_id' => $projectIds[2], 'start_date' => now()->subDays(1), 'due_date' => now()->addDays(3)],
            ['title' => 'Basın davet listesi güncelle', 'status' => 'todo', 'priority' => 'medium', 'project_id' => $projectIds[2], 'start_date' => now()->addDays(2), 'due_date' => now()->addDays(7)],
            ['title' => 'Influencer brief dokümanı', 'status' => 'done', 'priority' => 'medium', 'project_id' => $projectIds[3], 'start_date' => now()->subDays(12), 'due_date' => now()->subDays(5), 'completed_at' => now()->subDays(6)],
            ['title' => 'Ürün fotoğraf çekimi koordinasyonu', 'status' => 'in_progress', 'priority' => 'high', 'project_id' => $projectIds[3], 'start_date' => now()->subDays(1), 'due_date' => now()->addDays(4)],
            ['title' => 'LinkedIn reklam kampanyası kurulumu', 'status' => 'todo', 'priority' => 'high', 'project_id' => $projectIds[4], 'start_date' => now()->addDays(1), 'due_date' => now()->addDays(3)],
            ['title' => 'Webinar landing page tasarımı', 'status' => 'in_progress', 'priority' => 'medium', 'project_id' => $projectIds[4], 'start_date' => now()->addDays(2), 'due_date' => now()->addDays(6)],
            ['title' => 'Haftalık performans raporu', 'status' => 'todo', 'priority' => 'low', 'start_date' => now(), 'due_date' => now()->addDays(2)],
            ['title' => 'Müşteri memnuniyet anketi gönder', 'status' => 'todo', 'priority' => 'medium', 'start_date' => now()->addDays(5), 'due_date' => now()->addDays(10)],
        ];

        foreach ($tasks as $t) {
            Task::updateOrCreate(
                ['title' => $t['title'], 'tenant_id' => $tenantId],
                array_merge($t, [
                    'tenant_id' => $tenantId,
                    'assigned_to' => $userId,
                    'created_by' => $userId,
                    'created_at' => $t['start_date'] ?? now(),
                ])
            );
        }

        // ── Campaigns ──────────────────────────────
        $campaigns = [
            ['title' => 'Yaz Kampanyası 2026', 'description' => 'Yıldız Holding yaz sezonu için entegre dijital kampanya', 'department_id' => $depts['Pazarlama'], 'goal' => 'Marka bilinirliği %15 artış', 'start_date' => now()->subDays(10), 'end_date' => now()->addDays(50), 'budget' => 250000, 'status' => 'active'],
            ['title' => 'Getir Yeni Özellik Lansmanı', 'description' => 'Uygulama güncelleme kampanyası — PR + sosyal medya', 'department_id' => $depts['Dijital'], 'goal' => '50K indirme hedefi', 'start_date' => now()->addDays(5), 'end_date' => now()->addDays(35), 'budget' => 180000, 'status' => 'planned'],
            ['title' => 'Karaca Sonbahar Koleksiyonu', 'description' => 'Influencer odaklı marka kampanyası', 'department_id' => $depts['Kreatif'], 'goal' => 'Satış %20 artış', 'start_date' => now()->subDays(30), 'end_date' => now()->subDays(2), 'budget' => 120000, 'status' => 'completed'],
        ];

        $campaignIds = [];
        foreach ($campaigns as $c) {
            $camp = Campaign::updateOrCreate(
                ['title' => $c['title'], 'tenant_id' => $tenantId],
                array_merge($c, ['tenant_id' => $tenantId])
            );
            $campaignIds[] = $camp->id;
        }

        // ── Events ─────────────────────────────────
        $events = [
            ['title' => 'İstanbul Kültür Festivali Basın Toplantısı', 'start_date' => now()->addDays(3), 'end_date' => now()->addDays(3), 'location' => 'Lütfi Kırdar Kongre Merkezi', 'description' => 'Festival programının basına tanıtımı'],
            ['title' => 'Yıldız Holding Q2 Sunum', 'start_date' => now()->addDays(7), 'end_date' => now()->addDays(7), 'location' => 'Maslak Yıldız Plaza', 'description' => 'Kampanya performans sunumu ve Q3 planlaması'],
            ['title' => 'Ekip Retrospektif Toplantısı', 'start_date' => now()->addDays(5), 'end_date' => now()->addDays(5), 'location' => 'Ofis', 'description' => 'Sprint retrospektifi ve süreç iyileştirme'],
            ['title' => 'Getir Lansman Etkinliği', 'start_date' => now()->addDays(12), 'end_date' => now()->addDays(12), 'location' => 'Zorlu PSM', 'description' => 'Yeni özellik lansmanı — basın ve influencer daveti'],
            ['title' => 'Karaca Fotoğraf Çekimi', 'start_date' => now()->addDays(2), 'end_date' => now()->addDays(2), 'location' => 'Stüdyo — Nişantaşı', 'description' => 'Sonbahar koleksiyonu ürün çekimi'],
        ];

        foreach ($events as $e) {
            Event::updateOrCreate(
                ['title' => $e['title'], 'tenant_id' => $tenantId],
                array_merge($e, ['tenant_id' => $tenantId])
            );
        }

        // ── Content Items ──────────────────────────
        $contents = [
            ['title' => 'Yıldız Holding Kurumsal Sunum', 'type' => 'presentation', 'body' => 'Q2 kampanya performans sunumu — 24 slayt', 'tone' => 'kurumsal', 'status' => 'approved', 'usage_count' => 3, 'department_id' => $depts['Kurumsal İletişim'], 'campaign_id' => $campaignIds[0]],
            ['title' => 'Getir Basın Bülteni', 'type' => 'text_block', 'body' => 'Getir yeni özellik lansmanı basın bülteni taslağı', 'tone' => 'haber', 'status' => 'draft', 'usage_count' => 0, 'department_id' => $depts['Medya'], 'campaign_id' => $campaignIds[1]],
            ['title' => 'Instagram Kampanya Görseli — Yaz', 'type' => 'image', 'body' => '1080x1080 — Yaz kampanyası ana görsel', 'tone' => 'canlı', 'status' => 'approved', 'usage_count' => 5, 'department_id' => $depts['Dijital'], 'campaign_id' => $campaignIds[0]],
            ['title' => 'Karaca Influencer Brief', 'type' => 'text_block', 'body' => 'Sonbahar koleksiyonu influencer çalışma brief dokümanı', 'tone' => 'samimi', 'status' => 'approved', 'usage_count' => 2, 'department_id' => $depts['Kreatif'], 'campaign_id' => $campaignIds[2]],
            ['title' => 'TechStar Webinar Scripti', 'type' => 'sales_script', 'body' => 'B2B lead generation webinar senaryosu — 45 dakika', 'tone' => 'teknik', 'status' => 'draft', 'usage_count' => 0, 'department_id' => $depts['Pazarlama']],
            ['title' => 'Festival Davet Metni', 'type' => 'text_block', 'body' => 'İstanbul Kültür Festivali VIP davet metni', 'tone' => 'resmi', 'status' => 'approved', 'usage_count' => 1, 'department_id' => $depts['Kurumsal İletişim']],
        ];

        foreach ($contents as $c) {
            ContentItem::updateOrCreate(
                ['title' => $c['title'], 'tenant_id' => $tenantId],
                array_merge($c, ['tenant_id' => $tenantId])
            );
        }

        // ── Social Posts ───────────────────────────
        $posts = [
            ['platform' => 'instagram', 'content' => '☀️ Yaz geldi, kampanyamız başladı! Yıldız Holding ile bu yaz çok farklı. #YazKampanyası #YıldızHolding', 'scheduled_at' => now()->addDays(1), 'status' => 'scheduled', 'campaign_id' => $campaignIds[0]],
            ['platform' => 'linkedin', 'content' => '🚀 Getir\'in yeni özelliğini test ettik. Lansman için geri sayım başladı! Detaylar yakında. #Getir #Lansman', 'scheduled_at' => now()->addDays(6), 'status' => 'draft', 'campaign_id' => $campaignIds[1]],
            ['platform' => 'twitter', 'content' => 'İstanbul Kültür Festivali basın toplantısına davetlisiniz! 📅 Detaylar bio\'da. #İstanbulFestival', 'scheduled_at' => now()->addDays(2), 'status' => 'scheduled'],
            ['platform' => 'instagram', 'content' => '🍂 Karaca yeni sezon koleksiyonu çok yakında! Ilk görseller burada. #KaracaHome #Sonbahar2026', 'scheduled_at' => now()->subDays(5), 'status' => 'published', 'campaign_id' => $campaignIds[2], 'published_url' => 'https://instagram.com/p/demo123'],
        ];

        foreach ($posts as $p) {
            SocialPost::updateOrCreate(
                ['content' => $p['content'], 'tenant_id' => $tenantId],
                array_merge($p, ['tenant_id' => $tenantId, 'created_by' => $userId])
            );
        }

        // ── Media Insights ─────────────────────────
        $insights = [
            ['source_type' => 'basın', 'content' => 'Yıldız Holding CEO Röportajı — Bloomberg HT. Şirketin dijital dönüşüm vizyonu ele alındı.', 'status' => 'processed'],
            ['source_type' => 'online', 'content' => 'Getir Yeni Yatırım Turu Haberi — Webrazzi. Yeni özellik lansmanı öncesi yoğun ilgi.', 'status' => 'new'],
            ['source_type' => 'tv', 'content' => 'İstanbul Kültür Festivali Hazırlıkları — NTV Haber. Festival programı ve sponsor bilgileri paylaşıldı.', 'status' => 'processed'],
        ];

        foreach ($insights as $i) {
            MediaInsight::updateOrCreate(
                ['content' => $i['content'], 'tenant_id' => $tenantId],
                array_merge($i, ['tenant_id' => $tenantId])
            );
        }

        $this->command->info("✅ Demo verisi başarıyla oluşturuldu!");
        $this->command->info("   → 5 Müşteri, 6 Proje, 12 Görev, 3 Kampanya, 5 Etkinlik, 6 İçerik, 4 Sosyal Post, 3 Medya");
    }
}
