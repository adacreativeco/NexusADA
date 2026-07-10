<?php

namespace App\Admin\Resources;

class IntegrationSettingConfig
{
    public static function config(): array
    {
        return [
            'model' => \App\Models\IntegrationSetting::class,
            'title' => 'Entegrasyonlar',
            'subtitle' => 'Webhook ve bildirim kanalları',
            'columns' => [
                [
                    'key' => 'provider',
                    'label' => 'Sağlayıcı',
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => ['slack' => 'purple', 'discord' => 'blue', 'generic_webhook' => 'gray'],
                    'format_map' => ['slack' => 'Slack', 'discord' => 'Discord', 'generic_webhook' => 'Genel Webhook'],
                ],
                ['key' => 'webhook_url', 'label' => 'Webhook URL', 'sortable' => false],
                [
                    'key' => 'is_active',
                    'label' => 'Durum',
                    'badge' => true,
                    'badge_colors' => [1 => 'green', 0 => 'gray'],
                    'format_map' => [1 => 'Aktif', 0 => 'Pasif'],
                ],
            ],
            'sections' => [
                [
                    'title' => 'Entegrasyon Ayarları',
                    'fields' => [
                        ['key' => 'provider', 'label' => 'Sağlayıcı', 'type' => 'select', 'required' => true, 'options' => [
                            'slack' => 'Slack',
                            'discord' => 'Discord',
                            'generic_webhook' => 'Genel Webhook',
                        ]],
                        ['key' => 'webhook_url', 'label' => 'Webhook URL', 'type' => 'text', 'required' => true, 'placeholder' => 'https://hooks.slack.com/...', 'span' => 'full'],
                        ['key' => 'is_active', 'label' => 'Aktif', 'type' => 'toggle'],
                    ],
                ],
            ],
            'default_sort' => 'provider',
        ];
    }
}
