<?php

namespace App\Admin\Resources;

class AutomationRuleConfig
{
    public static function config(): array
    {
        return [
            'model' => \App\Models\AutomationRule::class,
            'title' => 'Otomasyonlar',
            'subtitle' => 'İş akışı kuralları ve otomatik aksiyonlar',
            'icon' => 'bolt',
            'columns' => [
                ['key' => 'name', 'label' => 'Kural Adı', 'sortable' => true],
                [
                    'key' => 'trigger_model',
                    'label' => 'Tetikleyici',
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => ['Task' => 'blue', 'Project' => 'purple', 'Client' => 'green'],
                ],
                [
                    'key' => 'trigger_event',
                    'label' => 'Olay',
                    'badge' => true,
                    'badge_color' => 'gray',
                    'format_map' => [
                        'created' => 'Oluşturuldu',
                        'updated' => 'Güncellendi',
                        'status_changed' => 'Durum Değişti',
                        'deadline_passed' => 'Süre Doldu',
                    ],
                ],
                [
                    'key' => 'action_type',
                    'label' => 'Aksiyon',
                    'badge' => true,
                    'badge_colors' => [
                        'notify' => 'blue',
                        'change_status' => 'green',
                        'send_email' => 'purple',
                        'send_webhook' => 'yellow',
                        'assign_user' => 'indigo',
                    ],
                    'format_map' => [
                        'notify' => 'Bildirim',
                        'change_status' => 'Durum Değiştir',
                        'send_email' => 'E-posta',
                        'send_webhook' => 'Webhook',
                        'assign_user' => 'Kullanıcı Ata',
                    ],
                ],
                ['key' => 'execution_count', 'label' => 'Çalışma', 'sortable' => true, 'numeric' => true, 'suffix' => 'x'],
                [
                    'key' => 'is_active',
                    'label' => 'Durum',
                    'badge' => true,
                    'badge_colors' => [1 => 'green', 0 => 'gray'],
                    'format_map' => [1 => 'Aktif', 0 => 'Pasif'],
                ],
            ],
            'filters' => [
                [
                    'key' => 'trigger_model',
                    'label' => 'Model',
                    'options' => ['Task' => 'Görev', 'Project' => 'Proje', 'Client' => 'Müşteri'],
                ],
                [
                    'key' => 'is_active',
                    'label' => 'Durum',
                    'options' => [1 => 'Aktif', 0 => 'Pasif'],
                ],
            ],
            'sections' => [
                [
                    'title' => 'Kural Tanımı',
                    'fields' => [
                        ['key' => 'name', 'label' => 'Kural Adı', 'type' => 'text', 'required' => true, 'span' => 'full'],
                        ['key' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'span' => 'full'],
                        ['key' => 'is_active', 'label' => 'Aktif', 'type' => 'toggle'],
                    ],
                ],
                [
                    'title' => 'Tetikleyici',
                    'fields' => [
                        ['key' => 'trigger_model', 'label' => 'Model', 'type' => 'select', 'required' => true, 'options' => [
                            'Task' => 'Görev',
                            'Project' => 'Proje',
                            'Client' => 'Müşteri',
                        ]],
                        ['key' => 'trigger_event', 'label' => 'Olay', 'type' => 'select', 'required' => true, 'options' => [
                            'created' => 'Oluşturulduğunda',
                            'updated' => 'Güncellendiğinde',
                            'status_changed' => 'Durum Değiştiğinde',
                            'deadline_passed' => 'Süre Dolduğunda',
                        ]],
                    ],
                ],
                [
                    'title' => 'Koşul (Opsiyonel)',
                    'fields' => [
                        ['key' => 'condition_field', 'label' => 'Alan', 'type' => 'text', 'placeholder' => 'status, priority...'],
                        ['key' => 'condition_operator', 'label' => 'Operatör', 'type' => 'select', 'options' => [
                            'equals' => 'Eşittir',
                            'not_equals' => 'Eşit Değildir',
                            'contains' => 'İçerir',
                            'greater_than' => 'Büyüktür',
                        ]],
                        ['key' => 'condition_value', 'label' => 'Değer', 'type' => 'text', 'placeholder' => 'done, urgent...'],
                    ],
                ],
                [
                    'title' => 'Aksiyon',
                    'fields' => [
                        ['key' => 'action_type', 'label' => 'Tip', 'type' => 'select', 'required' => true, 'options' => [
                            'notify' => 'Bildirim Gönder',
                            'change_status' => 'Durum Değiştir',
                            'send_email' => 'E-posta Gönder',
                            'send_webhook' => 'Webhook Gönder',
                            'assign_user' => 'Kullanıcı Ata',
                        ]],
                    ],
                ],
            ],
            'default_sort' => 'name',
        ];
    }
}
