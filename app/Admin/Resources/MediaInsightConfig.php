<?php
namespace App\Admin\Resources;
use App\Models\MediaInsight;
use App\Models\Client;

class MediaInsightConfig
{
    public static function config(): array
    {
        return [
            'model' => MediaInsight::class,
            'title' => 'Medya Analizi',
            'subtitle' => 'Marka algısı ve medya yansımaları',
            'columns' => [
                ['key' => 'source_type', 'label' => 'Kaynak', 'searchable' => true, 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'news' => 'Haber', 'social_media' => 'Sosyal Medya', 'blog' => 'Blog', 'tv_radio' => 'TV/Radyo',
                ]],
                ['key' => 'project_name', 'label' => 'Proje', 'searchable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'pending' => 'warning', 'positive' => 'success', 'negative' => 'danger', 'neutral' => 'gray',
                ], 'format_map' => ['pending' => 'İnceleniyor', 'positive' => 'Pozitif', 'negative' => 'Negatif', 'neutral' => 'Nötr']],
                ['key' => 'created_at', 'label' => 'Tarih', 'datetime' => true, 'sortable' => true],
            ],
            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => ['pending' => 'İnceleniyor', 'positive' => 'Pozitif', 'negative' => 'Negatif', 'neutral' => 'Nötr']],
            ],
            'sections' => [
                ['title' => 'Medya Analizi Detayları', 'columns' => 2, 'fields' => [
                    ['key' => 'source_type', 'type' => 'select', 'label' => 'Kaynak Tipi', 'required' => true, 'options' => [
                        'news' => 'Haber', 'social_media' => 'Sosyal Medya', 'blog' => 'Blog/Köşe Yazısı', 'tv_radio' => 'TV/Radyo',
                    ]],
                    ['key' => 'project_name', 'type' => 'text', 'label' => 'İlgili Proje/Kampanya'],
                    ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'options' => [
                        'pending' => 'İnceleniyor', 'positive' => 'Pozitif', 'negative' => 'Negatif', 'neutral' => 'Nötr',
                    ]],
                    ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'model' => Client::class, 'display' => 'name'],
                    ['key' => 'content', 'type' => 'textarea', 'label' => 'Haber/İçerik Özeti', 'required' => true, 'span' => 'full'],
                ]],
            ],
        ];
    }
}
