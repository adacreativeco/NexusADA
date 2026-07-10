<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Work;
use App\Models\Activity;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Plan exists
        $plan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'is_active' => true,
                'max_users' => 10,
                'max_projects' => 100,
            ]
        );

        // 2. Ensure Tenant exists
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'ada-co-os'],
            [
                'name' => 'ADA Co-OS Creative Agency',
                'email' => 'contact@ada.co',
                'plan_id' => $plan->id,
                'status' => 'active',
            ]
        );

        // 3. Ensure Roles exist
        $webmasterRole = Role::firstOrCreate(['name' => 'webmaster', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // 4. Create 4 Users
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@dhe.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('webmaster');

        $adaAdminUser = User::updateOrCreate(
            ['email' => 'admin@adacreative.co'],
            [
                'name' => 'Ada Admin',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]
        );
        $adaAdminUser->assignRole('webmaster');

        $managerUser = User::updateOrCreate(
            ['email' => 'pm@dhe.com'],
            [
                'name' => 'Ayşe Yılmaz (PM)',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]
        );

        $developerUser = User::updateOrCreate(
            ['email' => 'dev@dhe.com'],
            [
                'name' => 'Mehmet Demir (Dev)',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]
        );

        $designerUser = User::updateOrCreate(
            ['email' => 'design@dhe.com'],
            [
                'name' => 'Elif Kaya (Designer)',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]
        );

        // 5. Create 5 Clients
        $clients = [];
        $clientNames = ['Beta Retail Corp', 'Acme Logistics Ltd', 'Global Tech Solutions', 'Creative Media Group', 'Pioneer Finance Inc'];
        foreach ($clientNames as $name) {
            $clients[] = Client::create([
                'name' => $name,
                'tenant_id' => $tenant->id,
                'decision_style' => 'Analytical',
                'risk_level' => 'Medium',
                'presentation_language' => 'Technical',
                'strategic_importance' => rand(1, 5),
                'behavioral_notes' => 'Appreciates thorough reports and data-driven insights.',
            ]);
        }

        // 5b. Create B2B Client Portal User
        \App\Models\ClientUser::updateOrCreate(
            ['email' => 'client@beta.com'],
            [
                'client_id' => $clients[0]->id,
                'name' => 'Beta Retail Temsilcisi',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        // 6. Create 10 Projects
        $projects = [];
        $projectTitles = [
            'E-Commerce Platform Redesign',
            'Warehouse Tracking WMS Integration',
            'SaaS Analytics Portal Development',
            'Brand Identity & Logo Redesign',
            'SEO Audit & Optimization Campaign',
            'API Gateway Orchestrator Setup',
            'Mobile Customer Care Application',
            'B2B Portal Customization',
            'Cybersecurity Infrastructure Review',
            'AI Helpdesk Copilot Integration'
        ];
        foreach ($projectTitles as $index => $title) {
            $projects[] = Project::create([
                'tenant_id' => $tenant->id,
                'client_id' => $clients[$index % count($clients)]->id,
                'title' => $title,
                'description' => 'Detailed description for project ' . $title,
                'start_date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(10, 60)),
                'budget' => rand(10000, 75000),
                'actual_revenue' => rand(5000, 60000),
                'hourly_rate' => 150,
                'planned_hours' => rand(50, 200),
            ]);
        }

        // 7. Create 5 Works (İşler)
        $works = [];
        $workTitles = [
            'Beta E-Commerce Launch Project',
            'Acme WMS Pilot Implementation',
            'Global Tech BI Integration',
            'Creative Media Brand Guidelines',
            'Pioneer Financial SEO Boost'
        ];
        $statuses = ['lead', 'proposal', 'started', 'in_progress', 'completed'];
        foreach ($workTitles as $index => $title) {
            $works[] = Work::create([
                'tenant_id' => $tenant->id,
                'client_id' => $clients[$index % count($clients)]->id,
                'project_id' => $projects[$index]->id,
                'title' => $title,
                'description' => 'A central business workflow managing client requirements for ' . $title,
                'status' => $statuses[$index],
                'priority' => ['low', 'medium', 'high', 'critical'][$index % 4],
                'value' => rand(20000, 100000),
                'currency' => 'TRY',
                'started_at' => now()->subDays(rand(1, 15)),
                'due_at' => now()->addDays(rand(15, 45)),
                'created_by' => $adminUser->id,
                'assigned_to' => $managerUser->id,
            ]);
        }

        // 8. Create 20 Tasks
        $taskTitles = [
            'Database schema modeling', 'Frontend template choice', 'User authentication flow', 'API integration tests',
            'Write user manuals', 'Final QA review & deployment', 'WMS API documentation review', 'Test webhook handlers',
            'Figma Concept Drafts', 'Prepare final logo SVG files', 'Conduct user interviews', 'Perform SEO keyword research',
            'Setup SSL certificates', 'Optimize SQL indices', 'Configure load balancer', 'Review GDPR compliance logs',
            'Create mail notification templates', 'Audit third-party packages', 'Build staging environment', 'Prepare user training slides'
        ];
        foreach ($taskTitles as $index => $title) {
            Task::create([
                'tenant_id' => $tenant->id,
                'project_id' => $projects[$index % count($projects)]->id,
                'work_id' => $works[$index % count($works)]->id,
                'title' => $title,
                'description' => 'Description of action required for task: ' . $title,
                'priority' => ['low', 'medium', 'high'][$index % 3],
                'status' => ['todo', 'in_progress', 'done'][$index % 3],
                'due_date' => now()->addDays(rand(-5, 20)),
                'assigned_to' => [$managerUser->id, $developerUser->id, $designerUser->id][$index % 3],
            ]);
        }

        // 9. Create 2 Demo Proposals
        $proposal1 = \App\Models\Proposal::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[0]->id,
            'work_id' => $works[0]->id,
            'title' => 'Kurumsal Web Tasarım Teklifi',
            'proposal_number' => 'TKL-2026-0001',
            'status' => 'pending_approval',
            'items' => [
                ['description' => 'Kullanıcı Deneyimi & Arayüz Tasarımı', 'quantity' => 1, 'unit_price' => 15000, 'vat_rate' => 20],
                ['description' => 'Laravel & Livewire Backend Geliştirme', 'quantity' => 1, 'unit_price' => 20000, 'vat_rate' => 20]
            ],
            'subtotal' => 35000.00,
            'tax_total' => 7000.00,
            'discount_total' => 0.00,
            'grand_total' => 42000.00,
            'currency' => 'TRY',
            'valid_until' => now()->addDays(14),
            'created_by' => $adminUser->id,
        ]);

        $proposal2 = \App\Models\Proposal::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[1]->id,
            'work_id' => $works[1]->id,
            'title' => 'SEO & Arama Motoru Optimizasyonu Teklifi',
            'proposal_number' => 'TKL-2026-0002',
            'status' => 'approved_internal',
            'items' => [
                ['description' => 'SEO Kelime Analizi & Teknik Optimizasyon', 'quantity' => 3, 'unit_price' => 6000, 'vat_rate' => 20]
            ],
            'subtotal' => 18000.00,
            'tax_total' => 3600.00,
            'discount_total' => 1000.00,
            'grand_total' => 20600.00,
            'currency' => 'TRY',
            'valid_until' => now()->addDays(7),
            'created_by' => $adminUser->id,
        ]);

        // 10. Create 2 Demo Contracts
        $contract1 = \App\Models\Contract::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[0]->id,
            'work_id' => $works[0]->id,
            'title' => 'Kurumsal Web Sitesi Destek Sözleşmesi',
            'contract_number' => 'SZL-2026-0001',
            'status' => 'active',
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(335),
            'value' => 18000.00,
            'currency' => 'TRY',
            'auto_renew' => true,
            'reminder_days' => 30,
            'created_by' => $adminUser->id,
        ]);

        $contract2 = \App\Models\Contract::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[1]->id,
            'work_id' => $works[1]->id,
            'title' => 'Yıllık Sosyal Medya Yönetim Sözleşmesi',
            'contract_number' => 'SZL-2026-0002',
            'status' => 'pending_approval',
            'start_date' => now(),
            'end_date' => now()->addDays(365),
            'value' => 24000.00,
            'currency' => 'TRY',
            'auto_renew' => false,
            'reminder_days' => 30,
            'created_by' => $adminUser->id,
        ]);

        // 11. Create Activities for Feed
        Activity::create([
            'tenant_id' => $tenant->id,
            'user_id' => $adminUser->id,
            'activity_type' => 'user',
            'model_type' => Work::class,
            'model_id' => $works[0]->id,
            'title' => 'İş Başlatıldı',
            'description' => 'System Admin ' . $works[0]->title . ' işini başlattı.',
        ]);

        Activity::create([
            'tenant_id' => $tenant->id,
            'activity_type' => 'automation',
            'model_type' => Work::class,
            'model_id' => $works[1]->id,
            'title' => 'Hatırlatma Gönderildi',
            'description' => 'Otomasyon ' . $works[1]->title . ' için ilk onay hatırlatmasını gönderdi.',
        ]);

        Activity::create([
            'tenant_id' => $tenant->id,
            'activity_type' => 'ai',
            'model_type' => Project::class,
            'model_id' => $projects[0]->id,
            'title' => 'Kârlılık Analizi',
            'description' => 'AI ' . $projects[0]->title . ' projesindeki kârlılık risklerini analiz etti.',
        ]);

        // 12. Create Bank Accounts
        $bankAccount1 = \App\Models\BankAccount::create([
            'tenant_id' => $tenant->id,
            'bank_name' => 'Garanti BBVA',
            'iban' => 'TR90 0006 2000 1234 5678 9012 34',
            'balance' => 145000.00,
            'currency' => 'TRY',
        ]);

        $bankAccount2 = \App\Models\BankAccount::create([
            'tenant_id' => $tenant->id,
            'bank_name' => 'Akbank USD',
            'iban' => 'TR80 0004 6000 9876 5432 1098 76',
            'balance' => 12000.00,
            'currency' => 'USD',
        ]);

        // 13. Create Incomes
        $income1 = \App\Models\Income::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[0]->id,
            'work_id' => $works[0]->id,
            'title' => 'Kurumsal Logo Tasarımı Hizmet Bedeli',
            'amount' => 25000.00,
            'tax_rate' => 20,
            'tax_amount' => 5000.00,
            'grand_total' => 30000.00,
            'currency' => 'TRY',
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
            'created_by' => $adminUser->id,
        ]);

        $income2 = \App\Models\Income::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[1]->id,
            'work_id' => $works[1]->id,
            'title' => 'Yıllık Sosyal Medya Hizmet Bedeli - Temmuz',
            'amount' => 8000.00,
            'tax_rate' => 20,
            'tax_amount' => 1600.00,
            'grand_total' => 9600.00,
            'currency' => 'TRY',
            'status' => 'sent',
            'created_by' => $adminUser->id,
        ]);

        // 14. Create Expenses
        $expense1 = \App\Models\Expense::create([
            'tenant_id' => $tenant->id,
            'vendor' => 'DHE Plaza A.Ş.',
            'title' => 'Ofis Kira Bedeli - Temmuz',
            'amount' => 15000.00,
            'tax_rate' => 20,
            'tax_amount' => 3000.00,
            'grand_total' => 18000.00,
            'currency' => 'TRY',
            'category' => 'office',
            'is_recurring' => true,
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
            'created_by' => $adminUser->id,
        ]);

        $expense2 = \App\Models\Expense::create([
            'tenant_id' => $tenant->id,
            'vendor' => 'RunPod GPU Cloud',
            'title' => 'Premium Yapay Zeka Sunucu Gideri',
            'amount' => 4500.00,
            'tax_rate' => 20,
            'tax_amount' => 900.00,
            'grand_total' => 5400.00,
            'currency' => 'TRY',
            'category' => 'software',
            'is_recurring' => true,
            'status' => 'pending_approval',
            'created_by' => $adminUser->id,
        ]);

        // 15. Create Collections
        $collection1 = \App\Models\Collection::create([
            'tenant_id' => $tenant->id,
            'client_id' => $clients[0]->id,
            'work_id' => $works[0]->id,
            'income_id' => $income1->id,
            'amount' => 30000.00,
            'currency' => 'TRY',
            'payment_method' => 'bank_transfer',
            'collected_at' => now(),
        ]);

        // 16. Create Financial Instruments
        \App\Models\FinancialInstrument::create([
            'tenant_id' => $tenant->id,
            'type' => 'check',
            'direction' => 'inbound',
            'instrument_number' => 'CHK-2026-9912',
            'amount' => 40000.00,
            'currency' => 'TRY',
            'due_date' => now()->addDays(7),
            'status' => 'pending',
        ]);

        // 17. Create AI Memories
        \App\Models\AIMemory::create([
            'tenant_id' => $tenant->id,
            'category' => 'style',
            'content' => 'LinkedIn ve sosyal medya paylaşımlarında samimi ama profesyonel bir Türkçe dili kullan.',
            'source' => 'admin_set',
            'is_active' => true,
        ]);

        \App\Models\AIMemory::create([
            'tenant_id' => $tenant->id,
            'category' => 'rule',
            'content' => 'Tekliflerde varsayılan KDV oranını %20 olarak belirle ve faturaları zamanında takip et.',
            'source' => 'admin_set',
            'is_active' => true,
        ]);

        // 18. Create Workflows
        \App\Models\Workflow::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kurumsal Logo & Kimlik Tasarım Akışı',
            'description' => 'Yeni kurumsal kimlik taleplerinde otomatik çalışan tasarım süreci akış şablonu.',
            'steps' => [
                ['label' => 'Müşteriyle Tasarım Ön Görüşmesi', 'role' => 'pm', 'action' => 'create_task'],
                ['label' => 'AI Marka Kimliği Analizi', 'role' => 'pm', 'action' => 'ai_analyze'],
                ['label' => 'Logo Konsept Çalışması', 'role' => 'designer', 'action' => 'create_task'],
                ['label' => 'Teklif Taslağının Üretilmesi', 'role' => 'designer', 'action' => 'create_proposal'],
            ],
            'is_active' => true,
        ]);

        // 19. Create Assets
        \App\Models\Asset::create([
            'tenant_id' => $tenant->id,
            'type' => 'color',
            'name' => 'ADA Kurumsal Renk Paleti',
            'description' => 'ADA Creative ajansı resmi ana ve yardımcı renk kodları.',
            'metadata' => '#10b981, #3b82f6, #6366f1, #f59e0b, #ef4444',
            'category' => 'Kurumsal',
            'tags' => ['brand', 'colors'],
            'created_by' => null,
        ]);

        // 20. Create Knowledge Articles
        \App\Models\KnowledgeArticle::create([
            'tenant_id' => $tenant->id,
            'type' => 'sop',
            'title' => 'Tasarım Teslim Standartları (SOP)',
            'content' => "### 🎨 Tasarım Teslim Standartları\nAjansımız bünyesindeki tüm tasarım teslimlerinde uyulması gereken standartlar:\n\n* **Format:** Tüm tasarımlar hem Figma bağlantısı hem de yüksek kaliteli PNG/PDF formatlarında sisteme yüklenmelidir.\n* **Renk Kodu:** Müşteriye sunulmadan önce renklerin CMYK ve RGB profilleri kontrol edilmiş olmalıdır.\n* **Onay Zinciri:** Tasarımlar sırasıyla PM ve Sanat Yönetmeni onayından geçtikten sonra müşteriye sunulur.",
            'category' => 'Tasarım',
            'tags' => ['design', 'sop'],
            'is_published' => true,
        ]);
    }
}
