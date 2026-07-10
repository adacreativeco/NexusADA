<?php

namespace App\Admin\Resources;

use App\Models\Collection;
use App\Models\Client;
use App\Models\Work;
use App\Models\Income;

class CollectionConfig
{
    public static function config(): array
    {
        return [
            'model' => Collection::class,
            'title' => 'Tahsilatlar',
            'subtitle' => 'Müşterilerden gelen ödeme girişleri ve hesap eşleşmeleri',

            'columns' => [
                ['key' => 'client.name', 'label' => 'Müşteri', 'searchable' => true, 'sortable' => true, 'relation' => 'client'],
                ['key' => 'work.title', 'label' => 'İş Süreci', 'searchable' => true, 'sortable' => true, 'relation' => 'work'],
                ['key' => 'income.income_number', 'label' => 'Fatura No', 'searchable' => true, 'relation' => 'income'],
                ['key' => 'amount', 'label' => 'Tutar', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'payment_method', 'label' => 'Yöntem', 'badge' => true, 'badge_color' => 'info', 'format_map' => [
                    'bank_transfer' => 'Havale / EFT',
                    'credit_card' => 'Kredi Kartı',
                    'cash' => 'Nakit',
                    'check' => 'Çek / Senet',
                ]],
                ['key' => 'collected_at', 'label' => 'Tahsil Tarihi', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'payment_method', 'label' => 'Ödeme Yöntemi', 'options' => [
                    'bank_transfer' => 'Havale / EFT',
                    'credit_card' => 'Kredi Kartı',
                    'cash' => 'Nakit',
                    'check' => 'Çek / Senet',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Tahsilat Bilgileri',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'required' => true, 'model' => Client::class, 'display' => 'name'],
                        ['key' => 'work_id', 'type' => 'belongs_to', 'label' => 'İş Süreci', 'model' => Work::class, 'display' => 'title'],
                        ['key' => 'income_id', 'type' => 'belongs_to', 'label' => 'İlişkili Fatura/Gelir', 'model' => Income::class, 'display' => 'income_number'],
                        ['key' => 'amount', 'type' => 'number', 'label' => 'Tahsil Edilen Tutar', 'required' => true, 'step' => '0.01'],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'required' => true, 'default' => 'TRY', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ]],
                        ['key' => 'payment_method', 'type' => 'select', 'label' => 'Ödeme Yöntemi', 'required' => true, 'default' => 'bank_transfer', 'options' => [
                            'bank_transfer' => 'Banka Havalesi / EFT',
                            'credit_card' => 'Kredi Kartı',
                            'cash' => 'Nakit',
                            'check' => 'Çek / Senet',
                        ]],
                        ['key' => 'collected_at', 'type' => 'date', 'label' => 'Tahsil Edilme Tarihi', 'required' => true],
                    ],
                ],
            ],
        ];
    }
}
