<?php

namespace App\Admin\Resources;

use App\Models\Expense;

class ExpenseConfig
{
    public static function config(): array
    {
        return [
            'model' => Expense::class,
            'title' => 'Giderler & Ödemeler',
            'subtitle' => 'Ofis harcamaları, yazılım üyelikleri, personel ve diğer işletme giderleri',

            'columns' => [
                ['key' => 'expense_number', 'label' => 'Gider No', 'searchable' => true, 'sortable' => true],
                ['key' => 'vendor', 'label' => 'Alıcı / Firma', 'searchable' => true, 'sortable' => true],
                ['key' => 'title', 'label' => 'Açıklama', 'searchable' => true, 'sortable' => true],
                ['key' => 'grand_total', 'label' => 'Toplam (KDV Dahil)', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'category', 'label' => 'Kategori', 'badge' => true, 'badge_color' => 'gray', 'format_map' => [
                    'personnel' => 'Personel & Maaş',
                    'software' => 'Yazılım / SaaS',
                    'office' => 'Ofis & Kira',
                    'marketing' => 'Pazarlama / Reklam',
                    'tax' => 'Vergi & Harç',
                    'other' => 'Diğer',
                ]],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'draft' => 'gray',
                    'pending_approval' => 'warning',
                    'approved_internal' => 'success',
                    'rejected_internal' => 'danger',
                    'paid' => 'success',
                ], 'format_map' => [
                    'draft' => 'Taslak',
                    'pending_approval' => 'Onay Bekliyor',
                    'approved_internal' => 'Onaylandı',
                    'rejected_internal' => 'Reddedildi',
                    'paid' => 'Ödendi',
                ]],
                ['key' => 'created_at', 'label' => 'Kayıt Tarihi', 'date' => true, 'sortable' => true],
            ],

            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'draft' => 'Taslak',
                    'pending_approval' => 'Onay Bekliyor',
                    'approved_internal' => 'Onaylandı',
                    'rejected_internal' => 'Reddedildi',
                    'paid' => 'Ödendi',
                ]],
                ['key' => 'category', 'label' => 'Kategori', 'options' => [
                    'personnel' => 'Personel & Maaş',
                    'software' => 'Yazılım / SaaS',
                    'office' => 'Ofis & Kira',
                    'marketing' => 'Pazarlama & Reklam',
                    'tax' => 'Vergi',
                    'other' => 'Diğer',
                ]],
            ],

            'sections' => [
                [
                    'title' => 'Gider Detayları',
                    'columns' => 2,
                    'fields' => [
                        ['key' => 'title', 'type' => 'text', 'label' => 'Gider Açıklaması', 'required' => true],
                        ['key' => 'vendor', 'type' => 'text', 'label' => 'Sağlayıcı / Alıcı Firma', 'required' => true],
                        ['key' => 'category', 'type' => 'select', 'label' => 'Harcama Kategorisi', 'required' => true, 'options' => [
                            'personnel' => 'Personel & Maaş',
                            'software' => 'Yazılım / SaaS',
                            'office' => 'Ofis & Kira',
                            'marketing' => 'Pazarlama & Reklam',
                            'tax' => 'Vergi',
                            'other' => 'Diğer',
                        ]],
                        ['key' => 'amount', 'type' => 'number', 'label' => 'Tutar (KDV Hariç)', 'required' => true, 'step' => '0.01'],
                        ['key' => 'tax_rate', 'type' => 'select', 'label' => 'KDV Oranı (%)', 'required' => true, 'default' => 20, 'options' => [
                            0 => 'KDV %0',
                            1 => 'KDV %1',
                            10 => 'KDV %10',
                            20 => 'KDV %20',
                        ]],
                        ['key' => 'currency', 'type' => 'select', 'label' => 'Döviz', 'required' => true, 'default' => 'TRY', 'options' => [
                            'TRY' => 'TRY (₺)',
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                        ]],
                        ['key' => 'is_recurring', 'type' => 'toggle', 'label' => 'Tekrarlayan Harcama', 'description' => 'Her ay otomatik mükerrer oluşturulsun'],
                        ['key' => 'status', 'type' => 'select', 'label' => 'Ödeme/Onay Durumu', 'required' => true, 'default' => 'draft', 'options' => [
                            'draft' => 'Taslak',
                            'pending_approval' => 'Yönetici Onayına Sun',
                            'approved_internal' => 'Onaylandı (Ödenebilir)',
                            'rejected_internal' => 'Reddedildi',
                            'paid' => 'Ödendi',
                        ]],
                        ['key' => 'payment_method', 'type' => 'select', 'label' => 'Ödeme Yöntemi', 'options' => [
                            'bank_transfer' => 'Banka Havalesi',
                            'credit_card' => 'Kredi Kartı',
                            'cash' => 'Nakit',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
