<?php
namespace App\Admin\Resources;
use App\Models\IncomingEmail;

class IncomingEmailConfig
{
    public static function config(): array
    {
        return [
            'model' => IncomingEmail::class,
            'title' => 'Gelen Kutusu',
            'subtitle' => 'E-posta takibi',
            'columns' => [
                ['key' => 'from', 'label' => 'Gönderen', 'searchable' => true, 'sortable' => true],
                ['key' => 'subject', 'label' => 'Konu', 'searchable' => true, 'sortable' => true],
                ['key' => 'received_at', 'label' => 'Tarih', 'datetime' => true, 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'unread' => 'danger', 'read' => 'gray', 'replied' => 'success',
                ], 'format_map' => ['unread' => 'Okunmadı', 'read' => 'Okundu', 'replied' => 'Yanıtlandı']],
            ],
            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => ['unread' => 'Okunmadı', 'read' => 'Okundu', 'replied' => 'Yanıtlandı']],
            ],
            'row_actions' => [
                [
                    'key' => 'mark_read',
                    'label' => 'Okundu İşaretle',
                    'icon_path' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    'visible' => fn ($r) => $r->status === 'unread',
                    'handler' => fn ($r) => $r->update(['status' => 'read']),
                ],
            ],
            'sections' => [
                ['title' => 'E-posta Detayları', 'columns' => 2, 'fields' => [
                    ['key' => 'from', 'type' => 'text', 'label' => 'Gönderen', 'required' => true],
                    ['key' => 'subject', 'type' => 'text', 'label' => 'Konu', 'required' => true],
                    ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'default' => 'unread', 'options' => [
                        'unread' => 'Okunmadı', 'read' => 'Okundu', 'replied' => 'Yanıtlandı',
                    ]],
                    ['key' => 'received_at', 'type' => 'datetime', 'label' => 'Alınma Tarihi'],
                    ['key' => 'body', 'type' => 'textarea', 'label' => 'İçerik', 'span' => 'full', 'rows' => 6],
                ]],
            ],
        ];
    }
}
