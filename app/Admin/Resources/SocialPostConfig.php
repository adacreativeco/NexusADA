<?php

namespace App\Admin\Resources;

class SocialPostConfig
{
    public static function config(): array
    {
        $campaigns = \App\Models\Campaign::orderBy('title')->pluck('title', 'id')->toArray();

        return [
            'label' => 'Sosyal Medya',
            'model' => \App\Models\SocialPost::class,
            'searchable' => ['content', 'notes'],
            'default_sort' => ['scheduled_at', 'desc'],

            'columns' => [
                ['key' => 'platform', 'label' => 'Platform', 'sortable' => true,
                    'format_map' => ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'twitter' => 'X/Twitter', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'],
                    'badge_map' => ['instagram' => 'danger', 'facebook' => 'info', 'linkedin' => 'info', 'twitter' => 'gray', 'youtube' => 'danger', 'tiktok' => 'warning'],
                ],
                ['key' => 'content', 'label' => 'İçerik', 'limit' => 60],
                ['key' => 'status', 'label' => 'Durum', 'sortable' => true,
                    'format_map' => ['draft' => 'Taslak', 'scheduled' => 'Planlandı', 'published' => 'Yayında', 'cancelled' => 'İptal'],
                    'badge_map' => ['draft' => 'gray', 'scheduled' => 'warning', 'published' => 'success', 'cancelled' => 'danger'],
                ],
                ['key' => 'campaign.title', 'label' => 'Kampanya'],
                ['key' => 'scheduled_at', 'label' => 'Tarih', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'platform', 'label' => 'Platform', 'options' => [
                    '' => 'Tümü', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'twitter' => 'X/Twitter', 'youtube' => 'YouTube', 'tiktok' => 'TikTok',
                ]],
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    '' => 'Tümü', 'draft' => 'Taslak', 'scheduled' => 'Planlandı', 'published' => 'Yayında', 'cancelled' => 'İptal',
                ]],
            ],

            'with' => ['campaign'],

            'sections' => [
                [
                    'title' => 'Paylaşım Bilgileri',
                    'fields' => [
                        ['key' => 'platform', 'label' => 'Platform', 'type' => 'select', 'required' => true, 'options' => [
                            'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn',
                            'twitter' => 'X/Twitter', 'youtube' => 'YouTube', 'tiktok' => 'TikTok',
                        ]],
                        ['key' => 'status', 'label' => 'Durum', 'type' => 'select', 'default' => 'draft', 'options' => [
                            'draft' => 'Taslak', 'scheduled' => 'Planlandı', 'published' => 'Yayında', 'cancelled' => 'İptal',
                        ]],
                        ['key' => 'content', 'label' => 'İçerik', 'type' => 'textarea', 'required' => true, 'span' => 2, 'rows' => 4],
                        ['key' => 'campaign_id', 'label' => 'Kampanya', 'type' => 'select', 'options' => ['' => 'Seçiniz'] + $campaigns],
                        ['key' => 'scheduled_at', 'label' => 'Planlanan Tarih/Saat', 'type' => 'datetime', 'required' => true],
                        ['key' => 'media_url', 'label' => 'Medya URL', 'type' => 'text', 'span' => 2, 'placeholder' => 'Görsel veya video bağlantısı'],
                        ['key' => 'published_url', 'label' => 'Yayın URL', 'type' => 'text', 'span' => 2, 'placeholder' => 'Yayınlanınca linki'],
                        ['key' => 'notes', 'label' => 'Notlar', 'type' => 'textarea', 'span' => 2],
                    ],
                ],
            ],

            'row_actions' => [],
        ];
    }
}
