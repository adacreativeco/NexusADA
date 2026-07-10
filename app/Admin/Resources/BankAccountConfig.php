<?php

namespace App\Admin\Resources;

use App\Models\BankAccount;

class BankAccountConfig
{
    public static function config(): array
    {
        return [
            'model' => BankAccount::class,
            'title' => 'Banka Hesapları',
            'subtitle' => 'Finansal bakiye ve nakit yönetimi hesapları',

            'columns' => [
                ['key' => 'bank_name', 'label' => 'Banka Adı', 'searchable' => true, 'sortable' => true],
                ['key' => 'iban', 'label' => 'IBAN', 'searchable' => true],
                ['key' => 'balance', 'label' => 'Bakiye', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'currency', 'label' => 'Döviz', 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'currency', 'label' => 'Döviz Tipi', 'options' => [
                    'TRY' => 'TRY (₺)',
                    'USD' => 'USD ($)',
                    'EUR' => 'EUR (€)',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Hesap Bilgileri',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'bank_name', 'type' => 'text', 'label' => 'Banka Adı', 'required' => true],
                        ['key' => 'iban', 'type' => 'text', 'label' => 'IBAN Numarası'],
                        ['key' => 'balance', 'type' => 'number', 'label' => 'Güncel Bakiye', 'step' => '0.01', 'default' => 0.00],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Döviz Birimi', 'required' => true, 'default' => 'TRY', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
