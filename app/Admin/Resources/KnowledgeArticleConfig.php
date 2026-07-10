<?php

namespace App\Admin\Resources;

class KnowledgeArticleConfig
{
    public static function config(): array
    {
        return [
            'title' => __('Bilgi Bankası (SOP & Wiki)'),
            'model' => \App\Models\KnowledgeArticle::class,
            'columns' => [
                [
                    'key' => 'title',
                    'label' => __('Makale Başlığı'),
                    'sortable' => true,
                    'searchable' => true,
                ],
                [
                    'key' => 'type',
                    'label' => __('Makale Tipi'),
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => [
                        'sop' => 'warning',
                        'checklist' => 'success',
                        'wiki' => 'info',
                        'training' => 'primary',
                        'documentation' => 'gray',
                    ],
                    'format_map' => [
                        'sop' => 'Standart Süreç (SOP)',
                        'checklist' => 'Kontrol Listesi (Checklist)',
                        'wiki' => 'Şirket Wiki',
                        'training' => 'Eğitim Materyali',
                        'documentation' => 'Dokümantasyon',
                    ],
                ],
                [
                    'key' => 'category',
                    'label' => __('Kategori'),
                    'sortable' => true,
                ],
                [
                    'key' => 'is_published',
                    'label' => __('Yayın Durumu'),
                    'sortable' => true,
                    'badge' => true,
                    'badge_colors' => [
                        1 => 'success',
                        0 => 'gray',
                    ],
                    'format_map' => [
                        1 => 'Yayında',
                        0 => 'Taslak',
                    ],
                ],
            ],
            'sections' => [
                [
                    'title' => __('Makale Detayları'),
                    'fields' => [
                        [
                            'key' => 'title',
                            'label' => __('Makale Başlığı'),
                            'type' => 'text',
                            'required' => true,
                        ],
                        [
                            'key' => 'type',
                            'label' => __('Tip'),
                            'type' => 'select',
                            'required' => true,
                            'options' => [
                                'sop' => 'Standart Süreç (SOP)',
                                'checklist' => 'Kontrol Listesi (Checklist)',
                                'wiki' => 'Şirket Wiki',
                                'training' => 'Eğitim Materyali',
                                'documentation' => 'Dokümantasyon',
                            ],
                            'default' => 'wiki',
                        ],
                        [
                            'key' => 'category',
                            'label' => __('Kategori (örn: Tasarım, Yazılım, IK)'),
                            'type' => 'text',
                        ],
                        [
                            'key' => 'is_published',
                            'label' => __('Yayınla?'),
                            'type' => 'toggle',
                            'default' => true,
                        ],
                        [
                            'key' => 'content',
                            'label' => __('İçerik (Markdown & Checklist Destekli)'),
                            'type' => 'textarea',
                            'required' => true,
                            'span' => 2,
                        ],
                    ],
                ],
            ],
        ];
    }
}
