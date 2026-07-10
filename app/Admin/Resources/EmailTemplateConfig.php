<?php

namespace App\Admin\Resources;

class EmailTemplateConfig
{
    public static function config(): array
    {
        return [
            'label' => 'E-posta Şablonları',
            'model' => \App\Models\EmailTemplate::class,
            'searchable' => ['name', 'subject', 'body'],
            'default_sort' => ['name', 'asc'],

            'columns' => [
                ['key' => 'name', 'label' => 'Şablon Adı', 'sortable' => true],
                ['key' => 'subject', 'label' => 'Konu'],
                ['key' => 'category', 'label' => 'Kategori', 'sortable' => true,
                    'format_map' => ['proposal' => 'Teklif', 'follow_up' => 'Takip', 'meeting' => 'Toplantı', 'welcome' => 'Hoşgeldin', 'thank_you' => 'Teşekkür'],
                    'badge_map' => ['proposal' => 'info', 'follow_up' => 'warning', 'meeting' => 'gray', 'welcome' => 'success', 'thank_you' => 'success'],
                ],
                ['key' => 'is_default', 'label' => 'Varsayılan', 'boolean' => true],
            ],

            'filters' => [
                ['key' => 'category', 'label' => 'Kategori', 'options' => [
                    '' => 'Tümü', 'proposal' => 'Teklif', 'follow_up' => 'Takip', 'meeting' => 'Toplantı', 'welcome' => 'Hoşgeldin', 'thank_you' => 'Teşekkür',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Şablon Bilgileri',
                    'fields' => [
                        ['key' => 'name', 'label' => 'Şablon Adı', 'type' => 'text', 'required' => true],
                        ['key' => 'category', 'label' => 'Kategori', 'type' => 'select', 'default' => 'proposal', 'options' => [
                            'proposal' => 'Teklif', 'follow_up' => 'Takip', 'meeting' => 'Toplantı', 'welcome' => 'Hoşgeldin', 'thank_you' => 'Teşekkür',
                        ]],
                        ['key' => 'subject', 'label' => 'E-posta Konusu', 'type' => 'text', 'required' => true, 'span' => 2],
                        ['key' => 'body', 'label' => 'İçerik (HTML)', 'type' => 'textarea', 'required' => true, 'span' => 2, 'rows' => 10,
                            'placeholder' => 'Değişkenler: {{client_name}}, {{project_name}}, {{date}}'],
                        ['key' => 'is_default', 'label' => 'Varsayılan Şablon', 'type' => 'toggle', 'description' => 'Bu kategori için varsayılan olarak kullan'],
                    ],
                ],
            ],

            'row_actions' => [],
        ];
    }
}
