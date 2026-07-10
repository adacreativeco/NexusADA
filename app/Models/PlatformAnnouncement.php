<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformAnnouncement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Şu anda gösterilmesi gereken aktif duyurular
     */
    public function scopeCurrentlyActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest();
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'warning' => 'warning',
            'critical' => 'danger',
            'feature' => 'success',
            default => 'info',
        };
    }
}
