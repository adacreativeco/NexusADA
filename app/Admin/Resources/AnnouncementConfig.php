<?php
namespace App\Admin\Resources;
use App\Models\PlatformAnnouncement;

class AnnouncementConfig
{
    public static function config(): array
    {
        return [
            'model' => PlatformAnnouncement::class,
            'title' => 'Duyurular',
            'subtitle' => 'Platform duyuru yönetimi',
            'columns' => [
                ['key' => 'title', 'label' => 'Başlık', 'searchable' => true, 'sortable' => true],
                ['key' => 'type', 'label' => 'Tür', 'badge' => true, 'badge_colors' => [
                    'info' => 'info', 'warning' => 'warning', 'critical' => 'danger', 'feature' => 'success',
                ], 'format_map' => [
                    'info' => 'Bilgi', 'warning' => 'Uyarı', 'critical' => 'Kritik', 'feature' => 'Yeni Özellik',
                ]],
                ['key' => 'is_active', 'label' => 'Aktif', 'badge' => true, 'badge_colors' => [
                    '1' => 'success', '0' => 'gray',
                ], 'format_map' => ['1' => 'Aktif', '0' => 'Pasif']],
                ['key' => 'starts_at', 'label' => 'Başlangıç', 'datetime' => true, 'sortable' => true],
                ['key' => 'ends_at', 'label' => 'Bitiş', 'datetime' => true],
            ],
            'sections' => [
                ['title' => 'Duyuru Bilgileri', 'columns' => 2, 'fields' => [
                    ['key' => 'title', 'type' => 'text', 'label' => 'Başlık', 'required' => true],
                    ['key' => 'type', 'type' => 'select', 'label' => 'Tür', 'required' => true, 'default' => 'info', 'options' => [
                        'info' => 'Bilgi', 'warning' => 'Uyarı', 'critical' => 'Kritik', 'feature' => 'Yeni Özellik',
                    ]],
                    ['key' => 'body', 'type' => 'textarea', 'label' => 'İçerik', 'required' => true, 'span' => 'full', 'rows' => 4],
                    ['key' => 'is_active', 'type' => 'toggle', 'label' => 'Aktif', 'default' => true],
                    ['key' => 'starts_at', 'type' => 'datetime', 'label' => 'Başlangıç Tarihi'],
                    ['key' => 'ends_at', 'type' => 'datetime', 'label' => 'Bitiş Tarihi'],
                ]],
            ],
        ];
    }
}
