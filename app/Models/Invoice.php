<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Bekliyor',
            'paid' => 'Ödendi',
            'failed' => 'Başarısız',
            'refunded' => 'İade Edildi',
            default => $this->status,
        };
    }

    /**
     * Otomatik fatura numarası oluştur
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $lastInvoice = static::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastInvoice
            ? (int) substr($lastInvoice->invoice_number, -4) + 1
            : 1;

        return sprintf('INV-%d-%04d', $year, $sequence);
    }
}
