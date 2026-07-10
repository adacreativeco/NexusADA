<?php

namespace App\Admin\Resources;

class WorkflowConfig
{
    public static function config(): array
    {
        return [
            'title' => __('İş Akışları (Workflows)'),
            'model' => \App\Models\Workflow::class,
            'columns' => [
                [
                    'key' => 'name',
                    'label' => __('İş Akışı Adı'),
                    'sortable' => true,
                    'searchable' => true,
                ],
                [
                    'key' => 'description',
                    'label' => __('Açıklama'),
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
                    'title' => __('Temel Bilgiler'),
                    'fields' => [
                        [
                            'key' => 'name',
                            'label' => __('İş Akışı Adı'),
                            'type' => 'text',
                            'required' => true,
                        ],
                        [
                            'key' => 'description',
                            'label' => __('Açıklama'),
                            'type' => 'textarea',
                        ],
                        [
                            'key' => 'is_active',
                            'label' => __('Aktif mi?'),
                            'type' => 'toggle',
                            'default' => true,
                        ],
                        [
                            'key' => 'steps',
                            'label' => __('Adımlar (JSON)'),
                            'type' => 'hidden',
                            'default' => [],
                        ],
                    ],
                ],
            ],
            'row_actions' => [
                [
                    'key' => 'design',
                    'label' => __('Tasarım Editörü'),
                    'url' => '/admin/workflows/{id}/design',
                    'icon_path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ],
            ],
        ];
    }
}
