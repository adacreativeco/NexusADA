<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'expected_users' => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByPlan($query, string $plan)
    {
        return $query->where('plan_interest', $plan);
    }

    // ── Helpers ─────────────────────────────────────────

    public function markContacted(?string $notes = null): void
    {
        $this->update([
            'status' => 'contacted',
            'notes'  => $notes ?? $this->notes,
        ]);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new'       => 'Yeni',
            'contacted' => 'İletişime Geçildi',
            'converted' => 'Dönüştürüldü',
            'rejected'  => 'Reddedildi',
            default     => $this->status,
        };
    }

    public function planLabel(): string
    {
        return match ($this->plan_interest) {
            'pro'        => 'Profesyonel',
            'enterprise' => 'Kurumsal',
            default      => $this->plan_interest,
        };
    }
}
