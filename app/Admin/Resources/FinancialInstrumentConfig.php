<?php

namespace App\Admin\Resources;

use App\Models\FinancialInstrument;

class FinancialInstrumentConfig
{
    public static function config(): array
    {
        return [
            'model' => FinancialInstrument::class,
            'title' => 'Çek & Senetler',
            'subtitle' => 'Alınan ve verilen çek, senet, vadeli kıymetli evrak takibi',

            'columns' => [
                ['key' => 'instrument_number', 'label' => 'Evrak No', 'searchable' => true, 'sortable' => true],
                ['key' => 'type', 'label' => 'Evrak Türü', 'badge' => true, 'badge_colors' => [
                    'check' => 'info',
                    'promissory_note' => 'warning',
                ], 'format_map' => [
                    'check' => 'Çek',
                    'promissory_note' => 'Senet',
                ]],
                ['key' => 'direction', 'label' => 'Yön', 'badge' => true, 'badge_colors' => [
                    'inbound' => 'success',
                    'outbound' => 'danger',
                ], 'format_map' => [
                    'inbound' => 'Alınan',
                    'outbound' => 'Verilen',
                ]],
                ['key' => 'amount', 'label' => 'Tutar', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'due_date', 'label' => 'Vade Tarihi', 'date' => true, 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'pending' => 'warning',
                    'paid' => 'success',
                    'bounced' => 'danger',
                    'cancelled' => 'gray',
                ], 'format_map' => [
                    'pending' => 'Beklemede',
                    'paid' => 'Ödendi / Tahsil Edildi',
                    'bounced' => 'Karşılıksız / Protestolu',
                    'cancelled' => 'İptal',
                ]],
            ],

            'filters' => [
                ['key' => 'type', 'label' => 'Tür', 'options' => [
                    'check' => 'Çek',
                    'promissory_note' => 'Senet',
                ]],
                ['key' => 'direction', 'label' => 'Yön', 'options' => [
                    'inbound' => 'Alınan',
                    'outbound' => 'Verilen',
                ]],
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'pending' => 'Beklemede',
                    'paid' => 'Ödendi',
                    'bounced' => 'Karşılıksız',
                    'cancelled' => 'İptal',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Evrak Detayları',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'instrument_number', 'type' => 'text', 'label' => 'Evrak No', 'required' => true],
                        ['key' => 'type', 'type' => 'select', 'label' => 'Evrak Türü', 'required' => true, 'options' => [
                            'check' => 'Çek',
                            'promissory_note' => 'Senet',
                        ]],
                        ['key' => 'direction', 'type' => 'select', 'label' => 'Evrak Yönü', 'required' => true, 'options' => [
                            'inbound' => 'Alınan (Giriş)',
                            'outbound' => 'Verilen (Çıkış)',
                        ]],
                        ['key' => 'amount', 'type' => 'number', 'label' => 'Tutar', 'required' => true, 'step' => '0.01'],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'required' => true, 'default' => 'TRY', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ]],
                        ['key' => 'due_date', 'type' => 'date', 'label' => 'Vade Tarihi', 'required' => true],
                        ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'default' => 'pending', 'options' => [
                            'pending' => 'Beklemede / Tahsil Edilmedi',
                            'paid' => 'Ödendi / Tahsil Edildi',
                            'bounced' => 'Karşılıksız / Protestolu',
                            'cancelled' => 'İptal Edildi',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
