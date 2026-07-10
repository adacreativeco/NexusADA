<?php

namespace App\Admin\Resources;

use App\Models\Work;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;

class WorkConfig
{
    public static function config(): array
    {
        return [
            'model' => Work::class,
            'title' => 'İşler',
            'subtitle' => 'İş süreçleri, teklif, sözleşme ve timeline yönetimi',

            'columns' => [
                ['key' => 'title', 'label' => 'İş Başlığı', 'searchable' => true, 'sortable' => true],
                ['key' => 'client.name', 'label' => 'Müşteri', 'searchable' => true, 'sortable' => true, 'relation' => 'client'],
                ['key' => 'value', 'label' => 'Değer', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'lead' => 'Talep/Aday',
                    'proposal' => 'Teklif',
                    'approved' => 'Onaylandı',
                    'contract' => 'Sözleşme',
                    'started' => 'Başladı',
                    'in_progress' => 'Devam Ediyor',
                    'testing' => 'Test',
                    'delivery' => 'Teslimat',
                    'support' => 'Destek',
                    'completed' => 'Tamamlandı',
                    'cancelled' => 'İptal Edildi',
                ]],
                ['key' => 'priority', 'label' => 'Öncelik', 'badge' => true, 'badge_color' => 'warning', 'format_map' => [
                    'low' => 'Düşük',
                    'medium' => 'Orta',
                    'high' => 'Yüksek',
                    'critical' => 'Kritik',
                ]],
                ['key' => 'due_at', 'label' => 'Bitiş Tarihi', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'lead' => 'Talep/Aday',
                    'proposal' => 'Teklif',
                    'approved' => 'Onaylandı',
                    'contract' => 'Sözleşme',
                    'started' => 'Başladı',
                    'in_progress' => 'Devam Ediyor',
                    'testing' => 'Test',
                    'delivery' => 'Teslimat',
                    'support' => 'Destek',
                    'completed' => 'Tamamlandı',
                    'cancelled' => 'İptal Edildi',
                ]],
                ['key' => 'priority', 'label' => 'Öncelik', 'options' => [
                    'low' => 'Düşük',
                    'medium' => 'Orta',
                    'high' => 'Yüksek',
                    'critical' => 'Kritik',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'İş Tanımı',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'title', 'type' => 'text', 'label' => 'İş Başlığı', 'required' => true, 'span' => 'full', 'placeholder' => 'Örn: Web Sitesi Yenileme ve Geliştirme'],
                        ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'required' => true, 'model' => Client::class, 'display' => 'name'],
                        ['key' => 'project_id', 'type' => 'belongs_to', 'label' => 'İlişkili Proje (Opsiyonel)', 'required' => false, 'model' => Project::class, 'display' => 'title'],
                        ['key' => 'description', 'type' => 'textarea', 'label' => 'Açıklama / Süreç Detayları', 'span' => 'full', 'rows' => 4],
                    ],
                ],
                [
                    'title' => 'Süreç ve Finans',
                    'columns' => 3,
                    'fields' => [
                        ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'options' => [
                            'lead' => 'Talep/Aday',
                            'proposal' => 'Teklif',
                            'approved' => 'Onaylandı',
                            'contract' => 'Sözleşme',
                            'started' => 'Başladı',
                            'in_progress' => 'Devam Ediyor',
                            'testing' => 'Test',
                            'delivery' => 'Teslimat',
                            'support' => 'Destek',
                            'completed' => 'Tamamlandı',
                            'cancelled' => 'İptal Edildi',
                        ]],
                        ['key' => 'priority', 'type' => 'select', 'label' => 'Öncelik', 'options' => [
                            'low' => 'Düşük',
                            'medium' => 'Orta',
                            'high' => 'Yüksek',
                            'critical' => 'Kritik',
                        ]],
                        ['key' => 'value', 'type' => 'number', 'label' => 'Tahmini Değer (₺)', 'default' => 0],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ], 'default' => 'TRY'],
                        ['key' => 'started_at', 'type' => 'date', 'label' => 'Başlangıç Tarihi'],
                        ['key' => 'due_at', 'type' => 'date', 'label' => 'Bitiş Tarihi'],
                        ['key' => 'assigned_to', 'type' => 'belongs_to', 'label' => 'Atanan Sorumlu', 'model' => User::class, 'display' => 'name'],
                    ],
                ],
            ],

            'row_actions' => [
                ['key' => 'timeline', 'label' => 'Timeline', 'icon' => 'timeline', 'url' => '/admin/works/{id}/timeline'],
                [
                    'key' => 'start_workflow',
                    'label' => 'İş Akışını Başlat',
                    'icon_path' => 'M12 4.5v15m7.5-7.5h-15',
                ],
            ],
        ];
    }
}
