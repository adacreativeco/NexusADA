<?php
namespace App\Admin\Resources;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Plan;

class InvoiceConfig
{
    public static function config(): array
    {
        return [
            'model' => Invoice::class,
            'title' => 'Faturalar',
            'subtitle' => 'Abonelik fatura takibi',
            'columns' => [
                ['key' => 'invoice_number', 'label' => 'Fatura No', 'searchable' => true, 'sortable' => true],
                ['key' => 'tenant.name', 'label' => 'Kiracı', 'relation' => 'tenant', 'searchable' => true],
                ['key' => 'amount', 'label' => 'Tutar', 'money' => true, 'prefix' => '₺', 'sortable' => true],
                ['key' => 'status', 'label' => 'Durum', 'badge' => true, 'badge_colors' => [
                    'pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'gray',
                ], 'format_map' => [
                    'pending' => 'Bekliyor', 'paid' => 'Ödendi', 'failed' => 'Başarısız', 'refunded' => 'İade',
                ]],
                ['key' => 'billing_period_start', 'label' => 'Dönem Başı', 'date' => true, 'sortable' => true],
                ['key' => 'billing_period_end', 'label' => 'Dönem Sonu', 'date' => true],
            ],
            'filters' => [
                ['key' => 'status', 'label' => 'Durum', 'options' => [
                    'pending' => 'Bekliyor', 'paid' => 'Ödendi', 'failed' => 'Başarısız', 'refunded' => 'İade',
                ]],
            ],
            'sections' => [
                ['title' => 'Fatura Bilgileri', 'columns' => 2, 'fields' => [
                    ['key' => 'invoice_number', 'type' => 'text', 'label' => 'Fatura No', 'required' => true],
                    ['key' => 'tenant_id', 'type' => 'belongs_to', 'label' => 'Kiracı', 'required' => true, 'model' => Tenant::class, 'display' => 'name'],
                    ['key' => 'plan_id', 'type' => 'belongs_to', 'label' => 'Plan', 'model' => Plan::class, 'display' => 'name'],
                    ['key' => 'amount', 'type' => 'number', 'label' => 'Tutar (₺)', 'required' => true],
                    ['key' => 'currency', 'type' => 'select', 'label' => 'Para Birimi', 'default' => 'TRY', 'options' => [
                        'TRY' => 'TRY', 'USD' => 'USD', 'EUR' => 'EUR',
                    ]],
                    ['key' => 'status', 'type' => 'select', 'label' => 'Durum', 'required' => true, 'default' => 'pending', 'options' => [
                        'pending' => 'Bekliyor', 'paid' => 'Ödendi', 'failed' => 'Başarısız', 'refunded' => 'İade',
                    ]],
                    ['key' => 'billing_period_start', 'type' => 'date', 'label' => 'Dönem Başlangıcı', 'required' => true],
                    ['key' => 'billing_period_end', 'type' => 'date', 'label' => 'Dönem Bitişi', 'required' => true],
                    ['key' => 'payment_method', 'type' => 'select', 'label' => 'Ödeme Yöntemi', 'options' => [
                        'credit_card' => 'Kredi Kartı', 'bank_transfer' => 'Havale/EFT', 'other' => 'Diğer',
                    ]],
                    ['key' => 'payment_reference', 'type' => 'text', 'label' => 'Ödeme Referansı'],
                    ['key' => 'paid_at', 'type' => 'datetime', 'label' => 'Ödeme Tarihi'],
                    ['key' => 'notes', 'type' => 'textarea', 'label' => 'Notlar', 'span' => 'full'],
                ]],
            ],
        ];
    }
}
