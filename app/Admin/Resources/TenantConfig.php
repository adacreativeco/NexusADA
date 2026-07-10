<?php
namespace App\Admin\Resources;
use App\Models\Tenant;
use App\Models\Plan;

class TenantConfig
{
    public static function config(): array
    {
        return [
            'model' => Tenant::class,
            'title' => 'Kiracılar',
            'subtitle' => 'Platform kiracı yönetimi',
            'columns' => [
                ['key' => 'name', 'label' => 'Ajans / Şirket', 'searchable' => true, 'sortable' => true],
                ['key' => 'email', 'label' => 'E-posta', 'searchable' => true],
                ['key' => 'plan.name', 'label' => 'Plan', 'relation' => 'plan', 'badge' => true, 'badge_colors' => [
                    'Starter' => 'gray', 'Pro' => 'info', 'Enterprise' => 'warning',
                ]],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'pending' => 'warning', 'trial' => 'info', 'active' => 'success', 'past_due' => 'danger',
                    'suspended' => 'danger', 'cancelled' => 'gray',
                ], 'format_map' => [
                    'pending' => 'Onay Bekliyor', 'trial' => 'Deneme', 'active' => 'Aktif', 'past_due' => 'Ödeme Bekliyor',
                    'suspended' => 'Askıda', 'cancelled' => 'İptal',
                ]],
                ['key' => 'max_users', 'label' => 'Max Kullanıcı'],
                ['key' => 'created_at', 'label' => 'Kayıt', 'date' => true, 'sortable' => true],
            ],
            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'pending' => 'Onay Bekliyor', 'trial' => 'Deneme', 'active' => 'Aktif', 'past_due' => 'Ödeme Bekliyor',
                    'suspended' => 'Askıda', 'cancelled' => 'İptal',
                ]],
            ],
            'sections' => [
                ['title' => 'Kiracı Bilgileri', 'columns' => 2, 'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => 'Ajans / Şirket Adı', 'required' => true],
                    ['key' => 'slug', 'type' => 'text', 'label' => 'Slug', 'required' => true, 'placeholder' => 'ada-creative'],
                    ['key' => 'email', 'type' => 'text', 'label' => 'E-posta', 'required' => true],
                    ['key' => 'phone', 'type' => 'text', 'label' => 'Telefon'],
                    ['key' => 'domain', 'type' => 'text', 'label' => 'Custom Domain', 'placeholder' => 'panel.adacreative.co'],
                    ['key' => 'industry', 'type' => 'text', 'label' => 'Sektör', 'placeholder' => 'Reklam Ajansı'],
                    ['key' => 'tax_id', 'type' => 'text', 'label' => 'Vergi No'],
                    ['key' => 'address', 'type' => 'textarea', 'label' => 'Adres', 'span' => 'full'],
                ]],
                ['title' => 'Abonelik', 'columns' => 2, 'fields' => [
                    ['key' => 'plan_id', 'type' => 'belongs_to', 'label' => 'Plan', 'model' => Plan::class, 'display' => 'name'],
                    ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'default' => 'pending', 'options' => [
                        'pending' => 'Onay Bekliyor', 'trial' => 'Deneme', 'active' => 'Aktif', 'past_due' => 'Ödeme Bekliyor',
                        'suspended' => 'Askıda', 'cancelled' => 'İptal',
                    ]],
                    ['key' => 'trial_ends_at', 'type' => 'datetime', 'label' => 'Deneme Bitiş'],
                    ['key' => 'subscription_starts_at', 'type' => 'datetime', 'label' => 'Abonelik Başlangıç'],
                    ['key' => 'subscription_ends_at', 'type' => 'datetime', 'label' => 'Abonelik Bitiş'],
                    ['key' => 'max_users', 'type' => 'number', 'label' => 'Max Kullanıcı', 'default' => 5],
                    ['key' => 'max_projects', 'type' => 'number', 'label' => 'Max Proje', 'default' => 50],
                    ['key' => 'max_storage_mb', 'type' => 'number', 'label' => 'Max Depolama (MB)', 'default' => 1024],
                ]],
            ],
            'row_actions' => [
                [
                    'key' => 'impersonate',
                    'label' => 'Taklit Et',
                    'icon_path' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
                ],
            ],
        ];
    }
}
