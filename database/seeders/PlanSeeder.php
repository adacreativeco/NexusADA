<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Başlangıç',
                'slug' => 'starter',
                'description' => 'Tek kullanıcı için temel modüller. Hemen başlayın.',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'currency' => 'TRY',
                'max_users' => 1,
                'max_projects' => 3,
                'max_storage_mb' => 512,
                'max_campaigns' => 5,
                'features' => [
                    'crm' => true,
                    'projects' => true,
                    'reports' => true,
                    'web_access' => true,
                    'community_support' => true,
                ],
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Profesyonel',
                'slug' => 'pro',
                'description' => 'Büyüyen ekipler için tüm modüller ve öncelikli destek.',
                'price_monthly' => 2490.00,
                'price_yearly' => 24900.00,
                'currency' => 'TRY',
                'max_users' => 10,
                'max_projects' => 9999, // Sınırsız proje & kampanya
                'max_storage_mb' => 5120, // Tahmini 5GB
                'max_campaigns' => 9999,
                'features' => [
                    'crm' => true,
                    'projects' => true,
                    'pdf_reports' => true,
                    'email_sync' => true,
                    'desktop_app' => true,
                    'priority_support' => true,
                ],
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Kurumsal',
                'slug' => 'enterprise',
                'description' => 'Büyük organizasyonlar için özel kurulum ve SLA garantisi.',
                'price_monthly' => 0.00, // Özel teklif
                'price_yearly' => 0.00,
                'currency' => 'TRY',
                'max_users' => 9999, // Sınırsız kullanıcı
                'max_projects' => 9999, // Sınırsız
                'max_storage_mb' => 51200, // Tahmini 50GB
                'max_campaigns' => 9999,
                'features' => [
                    'self_hosted' => true,
                    'audit_trail' => true,
                    'api_access' => true,
                    'custom_integrations' => true,
                    'dedicated_manager' => true,
                ],
                'is_popular' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
