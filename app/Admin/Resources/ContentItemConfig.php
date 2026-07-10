<?php
namespace App\Admin\Resources;
use App\Models\ContentItem;
use App\Models\Campaign;
use App\Models\Department;

class ContentItemConfig
{
    public static function config(): array
    {
        return [
            'model' => ContentItem::class,
            'title' => 'İçerikler',
            'subtitle' => 'Kurumsal hafıza ve içerik yönetimi',
            'columns' => [
                ['key' => 'title', 'label' => 'Başlık', 'searchable' => true, 'sortable' => true],
                ['key' => 'type', 'label' => 'Tip', 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'text_block' => 'Metin', 'image' => 'Görsel', 'presentation' => 'Sunum', 'sales_script' => 'Satış',
                ]],
                ['key' => 'purpose', 'label' => 'Amaç', 'searchable' => true],
                ['key' => 'platform', 'label' => 'Platform'],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'draft' => 'warning', 'approved' => 'success', 'archived' => 'gray',
                ], 'format_map' => ['draft' => 'Taslak', 'approved' => 'Onaylı', 'archived' => 'Arşiv']],
                ['key' => 'created_at', 'label' => 'Tarih', 'datetime' => true, 'sortable' => true],
            ],
            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => ['draft' => 'Taslak', 'approved' => 'Onaylı', 'archived' => 'Arşiv']],
                ['key' => 'type', 'label' => 'Tip', 'options' => ['text_block' => 'Metin', 'image' => 'Görsel', 'presentation' => 'Sunum', 'sales_script' => 'Satış']],
            ],
            'row_actions' => [
                [
                    'key' => 'approve',
                    'label' => 'Onayla',
                    'icon_path' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    'visible' => fn ($record) => $record->status === 'draft',
                    'handler' => fn ($record) => $record->update(['status' => 'approved']),
                ],
                [
                    'key' => 'archive',
                    'label' => 'Arşivle',
                    'icon_path' => 'm20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
                    'visible' => fn ($record) => $record->status !== 'archived',
                    'handler' => fn ($record) => $record->update(['status' => 'archived']),
                ],
            ],
            'sections' => [
                ['title' => 'İçerik Detayları', 'columns' => 2, 'fields' => [
                    ['key' => 'title', 'type' => 'text', 'label' => 'Başlık', 'required' => true],
                    ['key' => 'type', 'type' => 'select', 'label' => 'İçerik Tipi', 'required' => true, 'options' => [
                        'text_block' => 'Metin Bloğu', 'image' => 'Görsel', 'presentation' => 'Sunum', 'sales_script' => 'Satış Senaryosu',
                    ]],
                    ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'default' => 'draft', 'options' => [
                        'draft' => 'Taslak', 'approved' => 'Onaylandı', 'archived' => 'Arşivlendi',
                    ]],
                    ['key' => 'campaign_id', 'type' => 'belongs_to', 'label' => 'Kampanya', 'model' => Campaign::class, 'display' => 'title'],
                    ['key' => 'department_id', 'type' => 'belongs_to', 'label' => 'Departman', 'model' => Department::class, 'display' => 'name'],
                    ['key' => 'purpose', 'type' => 'text', 'label' => 'Amaç', 'placeholder' => 'Örn: SEO, Algı, Teknik Bilgilendirme'],
                    ['key' => 'platform', 'type' => 'text', 'label' => 'Platform', 'placeholder' => 'Örn: LinkedIn, Blog'],
                    ['key' => 'body', 'type' => 'textarea', 'label' => 'İçerik', 'span' => 'full', 'rows' => 6],
                    ['key' => 'internal_notes', 'type' => 'textarea', 'label' => 'Dahili Notlar', 'span' => 'full'],
                ]],
                ['title' => 'Anlam & Etki Takibi', 'description' => 'Bu içerik ne işe yarıyor?', 'columns' => 2, 'fields' => [
                    ['key' => 'production_reason', 'type' => 'text', 'label' => 'Üretim Nedeni'],
                    ['key' => 'team_used', 'type' => 'text', 'label' => 'Hangi Ekip Kullandı?'],
                    ['key' => 'used_in_sales', 'type' => 'toggle', 'label' => 'Satışta Kullanıldı mı?'],
                    ['key' => 'is_reusable', 'type' => 'toggle', 'label' => 'Tekrar Kullanılabilir mi?', 'default' => true],
                ]],
            ],
        ];
    }
}
