<?php

namespace App\Admin\Resources;

use App\Models\Income;
use App\Models\Client;
use App\Models\Work;

class IncomeConfig
{
    public static function config(): array
    {
        return [
            'model' => Income::class,
            'title' => 'Gelirler & Faturalar',
            'subtitle' => 'Müşteri faturaları, hizmet bedelleri ve tahsilat takipleri',

            'columns' => [
                ['key' => 'income_number', 'label' => 'Gelir No', 'searchable' => true, 'sortable' => true],
                ['key' => 'title', 'label' => 'Açıklama', 'searchable' => true, 'sortable' => true],
                ['key' => 'client.name', 'label' => 'Müşteri', 'searchable' => true, 'sortable' => true, 'relation' => 'client'],
                ['key' => 'grand_total', 'label' => 'Toplam (KDV Dahil)', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'draft' => 'gray',
                    'sent' => 'info',
                    'paid' => 'success',
                    'cancelled' => 'danger',
                    'overdue' => 'danger',
                ], 'format_map' => [
                    'draft' => 'Taslak',
                    'sent' => 'Gönderildi',
                    'paid' => 'Ödendi',
                    'cancelled' => 'İptal',
                    'overdue' => 'Vadesi Geçmiş',
                ]],
                ['key' => 'created_at', 'label' => 'Kayıt Tarihi', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'draft' => 'Taslak',
                    'sent' => 'Gönderildi',
                    'paid' => 'Ödendi',
                    'cancelled' => 'İptal',
                    'overdue' => 'Vadesi Geçmiş',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Gelir Detayları',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'title', 'type' => 'text', 'label' => 'Fatura / Gelir Açıklaması', 'required' => true],
                        ['key' => 'client_id', 'type' => 'belongs_to', 'label' => 'Müşteri', 'model' => Client::class, 'display' => 'name'],
                        ['key' => 'work_id', 'type' => 'belongs_to', 'label' => 'İlişkili İş Süreci', 'model' => Work::class, 'display' => 'title'],
                        ['key' => 'amount', 'type' => 'number', 'label' => 'Matrah (KDV Hariç)', 'required' => true, 'step' => '0.01'],
                        ['key' => 'tax_rate', 'type' => 'select', 'label' => 'KDV Oranı (%)', 'required' => true, 'default' => 20, 'options' => [
                            0 => 'KDV %0 (Muaf)',
                            1 => 'KDV %1',
                            10 => 'KDV %10',
                            20 => 'KDV %20',
                        ]],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Döviz', 'required' => true, 'default' => 'TRY', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ]],
                        ['key' => 'status', 'type' => 'select', 'label' => 'Ödeme Durumu', 'required' => true, 'default' => 'draft', 'options' => [
                            'draft' => 'Taslak',
                            'sent' => 'Müşteriye Gönderildi',
                            'paid' => 'Ödendi / Tahsil Edildi',
                            'cancelled' => 'İptal Edildi',
                            'overdue' => 'Vadesi Geçmiş',
                        ]],
                        ['key' => 'payment_method', 'type' => 'select', 'label' => 'Ödeme Yöntemi', 'options' => [
                            'bank_transfer' => 'Banka Havalesi',
                            'credit_card' => 'Kredi Kartı',
                            'cash' => 'Nakit',
                            'check' => 'Çek / Senet',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
