<?php

namespace App\Admin\Resources;

use App\Models\Proposal;
use App\Models\Client;
use App\Models\Work;
use App\Models\User;

class ProposalConfig
{
    public static function config(): array
    {
        return [
            'model' => Proposal::class,
            'title' => 'Teklifler',
            'subtitle' => 'Müşteri fiyat teklifleri ve onay süreçleri yönetimi',

            'columns' => [
                ['key' => 'proposal_number', 'label' => 'Teklif No', 'searchable' => true, 'sortable' => true],
                ['key' => 'title', 'label' => 'Başlık', 'searchable' => true, 'sortable' => true],
                ['key' => 'client.name', 'label' => 'Müşteri', 'searchable' => true, 'sortable' => true, 'relation' => 'client'],
                ['key' => 'grand_total', 'label' => 'Toplam Tutar', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'draft' => 'Taslak',
                    'pending_approval' => 'Onay Bekliyor',
                    'approved_internal' => 'Onaylandı',
                    'rejected_internal' => 'Reddedildi',
                    'sent' => 'Gönderildi',
                    'viewed' => 'Görüldü',
                    'accepted' => 'Kabul Edildi',
                    'declined' => 'Reddedildi (Müşteri)',
                ]],
                ['key' => 'valid_until', 'label' => 'Geçerlilik Tarihi', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'draft' => 'Taslak',
                    'pending_approval' => 'Onay Bekliyor',
                    'approved_internal' => 'Onaylandı',
                    'rejected_internal' => 'Reddedildi',
                    'sent' => 'Gönderildi',
                    'accepted' => 'Kabul Edildi',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Teklif Detayı',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'title', 'type' => 'text', 'label' => 'Teklif Başlığı', 'required' => true, 'placeholder' => 'Örn: Web Tasarım ve Geliştirme Hizmeti'],
                        ['key' => 'proposal_number', 'type' => 'text', 'label' => 'Teklif Numarası', 'required' => true, 'default' => 'AUTO-GENERATE'],
                        ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'required' => true, 'model' => Client::class, 'display' => 'name'],
                        ['key' => 'work_id', 'type' => 'belongs_to', 'label' => 'İlişkili İş / Süreç (Opsiyonel)', 'required' => false, 'model' => Work::class, 'display' => 'title'],
                    ],
                ],
                [
                    'title' => 'Tarih ve Finansal Bilgiler',
                    'columns' => 3,
                    'fields' => [
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ], 'default' => 'TRY'],
                        ['key' => 'grand_total', 'type' => 'number', 'label' => 'Teklif Değeri', 'default' => 0],
                        ['key' => 'valid_until', 'type' => 'date', 'label' => 'Geçerlilik Tarihi'],
                        ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'options' => [
                            'draft' => 'Taslak',
                            'pending_approval' => 'Onay Bekliyor',
                            'approved_internal' => 'Onaylandı',
                            'rejected_internal' => 'Reddedildi',
                            'sent' => 'Gönderildi',
                            'accepted' => 'Kabul Edildi',
                        ], 'default' => 'draft'],
                    ],
                ],
            ],

            'row_actions' => [
                ['key' => 'view', 'label' => 'Görüntüle', 'icon' => 'visibility', 'url' => '/admin/proposals/{id}/view', 'target' => '_blank'],
                ['key' => 'builder', 'label' => 'Teklif Motoru', 'icon' => 'build', 'url' => '/admin/proposal?proposal_id={id}'],
            ],
        ];
    }
}
