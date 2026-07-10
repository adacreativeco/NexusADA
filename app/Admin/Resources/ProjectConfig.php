<?php

namespace App\Admin\Resources;

use App\Models\Project;
use App\Models\Client;

class ProjectConfig
{
    public static function config(): array
    {
        return [
            'model' => Project::class,
            'title' => 'Projeler',
            'subtitle' => 'Değer ERP\'si ve proje yönetimi',

            'columns' => [
                ['key' => 'title', 'label' => 'Proje Başlığı', 'searchable' => true, 'sortable' => true],
                ['key' => 'client.name', 'label' => 'Müşteri', 'searchable' => true, 'sortable' => true, 'relation' => 'client'],
                ['key' => 'usage_area', 'label' => 'Alan', 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'web' => 'Web', 'presentation' => 'Sunum', 'sales' => 'Satış', 'internal' => 'Dahili',
                ]],
                ['key' => 'budget', 'label' => 'Bütçe', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'actual_revenue', 'label' => 'Gelir', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'profitability_score', 'label' => 'Kârlılık %', 'numeric' => true, 'suffix' => '%', 'sortable' => true],
                ['key' => 'created_at', 'label' => 'Tarih', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'usage_area', 'label' => 'Kullanım Alanı', 'options' => [
                    'web' => 'Web Sitesi', 'presentation' => 'Sunum', 'sales' => 'Satış', 'internal' => 'Dahili',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Proje Kimliği',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'title', 'type' => 'text', 'label' => 'Proje Başlığı', 'required' => true, 'span' => 'full', 'placeholder' => 'Örn: Sosyal Medya Yönetimi'],
                        ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'required' => true, 'model' => Client::class, 'display' => 'name'],
                        ['key' => 'usage_area', 'type' => 'select', 'label' => 'Kullanım Alanı', 'options' => [
                            'web' => 'Web Sitesi', 'presentation' => 'Sunum', 'sales' => 'Satış', 'internal' => 'Dahili Proje',
                        ]],
                        ['key' => 'description', 'type' => 'textarea', 'label' => 'Proje Özeti & Hikayesi', 'span' => 'full', 'rows' => 4],
                    ],
                ],
                [
                    'title' => 'Değer Analizi (Valorization ERP)',
                    'description' => 'Bu projenin kuruma kattığı gerçek değer ve kârlılık',
                    'columns' => 3,
                    'fields' => [
                        ['key' => 'budget', 'type' => 'number', 'label' => 'Planlanan Bütçe', 'prefix' => '₺', 'default' => 0],
                        ['key' => 'actual_revenue', 'type' => 'number', 'label' => 'Gerçekleşen Gelir', 'prefix' => '₺', 'default' => 0],
                        ['key' => 'team_hours', 'type' => 'number', 'label' => 'Harcanan İş Gücü (Saat)', 'default' => 0],
                        ['key' => 'profitability_score', 'type' => 'number', 'label' => 'Kârlılık Skoru (0-100)', 'placeholder' => 'AI önerisi bekliyor...', 'max' => 100],
                        ['key' => 'sustainability_index', 'type' => 'number', 'label' => 'Sürdürülebilirlik İndeksi (0-100)', 'placeholder' => 'Hizmet döngüsü potansiyeli...', 'max' => 100],
                        ['key' => 'strategic_value_notes', 'type' => 'textarea', 'label' => 'Stratejik Değer Notları', 'span' => 'full', 'placeholder' => 'Bu proje neden önemli?'],
                    ],
                ],
            ],

            'row_actions' => [
                ['key' => 'pdf', 'label' => 'PDF', 'icon' => 'document-arrow-down', 'url' => '/admin/reports/project/{id}', 'target' => '_blank'],
            ],
        ];
    }
}
