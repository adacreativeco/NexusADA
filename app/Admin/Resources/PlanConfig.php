<?php
namespace App\Admin\Resources;
use App\Models\Plan;

class PlanConfig
{
    public static function config(): array
    {
        return [
            'model' => Plan::class,
            'title' => 'Planlar',
            'subtitle' => 'Abonelik planları yönetimi',
            'columns' => [
                ['key' => 'name', 'label' => 'Plan', 'searchable' => true, 'sortable' => true],
                ['key' => 'price_monthly', 'label' => 'Aylık', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'price_yearly', 'label' => 'Yıllık', 'money' => true, 'prefix' => '₺'],
                ['key' => 'max_users', 'label' => 'Max Kullanıcı', 'sortable' => true],
                ['key' => 'max_projects', 'label' => 'Max Proje'],
                ['key' => 'is_popular', 'label' => 'Popüler', 'badge' => true, 'badge_colors' => [
                    '1' => 'success', '0' => 'gray',
                ], 'format_map' => ['1' => 'Evet', '0' => 'Hayır']],
                ['key' => 'is_active', 'label' => 'Aktif', 'badge' => true, 'badge_colors' => [
                    '1' => 'success', '0' => 'danger',
                ], 'format_map' => ['1' => 'Aktif', '0' => 'Pasif']],
            ],
            'sections' => [
                ['title' => 'Plan Bilgileri', 'columns' => 2, 'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => 'Plan Adı', 'required' => true],
                    ['key' => 'slug', 'type' => 'text', 'label' => 'Slug', 'required' => true],
                    ['key' => 'description', 'type' => 'textarea', 'label' => 'Açıklama', 'span' => 'full'],
                ]],
                ['title' => 'Fiyatlandırma', 'columns' => 2, 'fields' => [
                    ['key' => 'price_monthly', 'type' => 'number', 'label' => 'Aylık Fiyat (₺)', 'required' => true],
                    ['key' => 'price_yearly', 'type' => 'number', 'label' => 'Yıllık Fiyat (₺)', 'required' => true],
                    ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'default' => 'TRY', 'options' => [
                        'TRY' => 'TRY', 'USD' => 'USD', 'EUR' => 'EUR',
                    ]],
                ]],
                ['title' => 'Limitler', 'columns' => 2, 'fields' => [
                    ['key' => 'max_users', 'type' => 'number', 'label' => 'Max Kullanıcı'],
                    ['key' => 'max_projects', 'type' => 'number', 'label' => 'Max Proje'],
                    ['key' => 'max_storage_mb', 'type' => 'number', 'label' => 'Max Depolama (MB)'],
                    ['key' => 'max_campaigns', 'type' => 'number', 'label' => 'Max Kampanya (boş = sınırsız)'],
                ]],
                ['title' => 'Görünürlük', 'columns' => 2, 'fields' => [
                    ['key' => 'is_popular', 'type' => 'toggle', 'label' => 'Popüler Plan'],
                    ['key' => 'is_active', 'type' => 'toggle', 'label' => 'Aktif', 'default' => true],
                    ['key' => 'sort_order', 'type' => 'number', 'label' => 'Sıralama', 'default' => 0],
                ]],
            ],
        ];
    }
}
