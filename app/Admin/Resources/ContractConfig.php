<?php

namespace App\Admin\Resources;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Work;

class ContractConfig
{
    public static function config(): array
    {
        return [
            'model' => Contract::class,
            'title' => 'Sözleşmeler',
            'subtitle' => 'Müşteri sözleşmeleri, hizmet süreleri ve otomatik yenileme takibi',

            'columns' => [
                ['key' => 'contract_number', 'label' => 'Sözleşme No', 'searchable' => true, 'sortable' => true],
                ['key' => 'title', 'label' => 'Başlık', 'searchable' => true, 'sortable' => true],
                ['key' => 'client.name', 'label' => 'Müşteri', 'searchable' => true, 'sortable' => true, 'relation' => 'client'],
                ['key' => 'value', 'label' => 'Değer', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'draft' => 'Taslak',
                    'pending_approval' => 'Onay Bekliyor',
                    'approved_internal' => 'Onaylandı',
                    'rejected_internal' => 'Reddedildi',
                    'active' => 'Aktif/Yürürlükte',
                    'expired' => 'Süresi Doldu',
                    'terminated' => 'Feshedildi',
                ]],
                ['key' => 'start_date', 'label' => 'Başlangıç', 'date' => true, 'sortable' => true],
                ['key' => 'end_date', 'label' => 'Bitiş', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'draft' => 'Taslak',
                    'pending_approval' => 'Onay Bekliyor',
                    'active' => 'Aktif',
                    'expired' => 'Süresi Doldu',
                    'terminated' => 'Feshedildi',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Sözleşme Tanımı',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'title', 'type' => 'text', 'label' => 'Sözleşme Başlığı', 'required' => true, 'placeholder' => 'Örn: 1 Yıllık SEO ve İçerik Yönetimi Sözleşmesi'],
                        ['key' => 'contract_number', 'type' => 'text', 'label' => 'Sözleşme Numarası', 'required' => true, 'default' => 'AUTO-GENERATE'],
                        ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'required' => true, 'model' => Client::class, 'display' => 'name'],
                        ['key' => 'work_id', 'type' => 'belongs_to', 'label' => 'İlişkili İş / Süreç (Opsiyonel)', 'required' => false, 'model' => Work::class, 'display' => 'title'],
                    ],
                ],
                [
                    'title' => 'Sözleşme Koşulları & Süre',
                    'columns' => 3,
                    'fields' => [
                        ['key' => 'value', 'type' => 'number', 'label' => 'Sözleşme Değeri', 'default' => 0],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ], 'default' => 'TRY'],
                        ['key' => 'start_date', 'type' => 'date', 'label' => 'Başlangıç Tarihi', 'required' => true],
                        ['key' => 'end_date', 'type' => 'date', 'label' => 'Bitiş Tarihi'],
                        ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'options' => [
                            'draft' => 'Taslak',
                            'pending_approval' => 'Onay Bekliyor',
                            'active' => 'Aktif',
                            'expired' => 'Süresi Doldu',
                            'terminated' => 'Feshedildi',
                        ], 'default' => 'draft'],
                        ['key' => 'auto_renew', 'type' => 'select', 'label' => 'Otomatik Yenileme', 'options' => [
                            '0' => 'Hayır',
                            '1' => 'Evet',
                        ], 'default' => '0'],
                    ],
                ],
            ],
        ];
    }
}
