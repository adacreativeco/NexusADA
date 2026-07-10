<?php

namespace App\Admin\Resources;

class AIMemoryConfig
{
    public static function config(): array
    {
        return [
            'title' => __('Yapay Zekâ Hafızası'),
            'model' => \App\Models\AIMemory::class,
            'has_interactions' => false,
            'columns' => [
                [
                    'key' => 'category',
                    'label' => __('Kategori'),
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => [
                        'style' => 'info',
                        'preference' => 'success',
                        'rule' => 'warning',
                        'fact' => 'gray',
                        'context' => 'primary',
                    ],
                    'format_map' => [
                        'style' => 'Marka Dili / Stil',
                        'preference' => 'Tercih',
                        'rule' => 'İş Kuralı',
                        'fact' => 'Bilgi / Gerçek',
                        'context' => 'Bağlam / Ortam',
                    ],
                ],
                [
                    'key' => 'content',
                    'label' => __('Hafıza İçeriği'),
                    'sortable' => false,
                ],
                [
                    'key' => 'source',
                    'label' => __('Kaynak'),
                    'sortable' => true,
                    'format_map' => [
                        'user_input' => 'Kullanıcı Girişi',
                        'ai_learned' => 'Yapay Zekâ Öğrenimi',
                        'admin_set' => 'Yönetici Tanımı',
                    ],
                ],
                [
                    'key' => 'is_active',
                    'label' => __('Durum'),
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => [
                        1 => 'success',
                        0 => 'danger',
                    ],
                    'format_map' => [
                        1 => 'Aktif',
                        0 => 'Pasif',
                    ],
                ],
            ],
            'sections' => [
                [
                    'title' => __('Hafıza Detayları'),
                    'fields' => [
                        [
                            'key' => 'category',
                            'label' => __('Kategori'),
                            'type' => 'select',
                            'required' => true,
                            'options' => [
                                'style' => 'Marka Dili / Stil',
                                'preference' => 'Tercih',
                                'rule' => 'İş Kuralı',
                                'fact' => 'Bilgi / Gerçek',
                                'context' => 'Bağlam / Ortam',
                            ],
                            'default' => 'style',
                        ],
                        [
                            'key' => 'content',
                            'label' => __('Bellek / Hafıza Metni'),
                            'type' => 'textarea',
                            'required' => true,
                            'placeholder' => __('Yapay zekânın hatırlamasını istediğiniz kural veya bilgiyi girin...'),
                        ],
                        [
                            'key' => 'is_active',
                            'label' => __('Aktif mi?'),
                            'type' => 'toggle',
                            'default' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
