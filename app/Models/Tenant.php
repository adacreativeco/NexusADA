<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'onboarded_at' => 'datetime',
    ];

    public function setMaxProjectsAttribute($value)
    {
        $val = $value === '' || $value === null ? 0 : (int)$value;
        $this->attributes['max_projects'] = min($val, 2147483647);
    }

    public function setMaxUsersAttribute($value)
    {
        $val = $value === '' || $value === null ? 0 : (int)$value;
        $this->attributes['max_users'] = min($val, 2147483647);
    }

    public function setMaxStorageMbAttribute($value)
    {
        $val = $value === '' || $value === null ? 0 : (int)$value;
        $this->attributes['max_storage_mb'] = min($val, 2147483647);
    }

    public function setSubscriptionEndsAtAttribute($value)
    {
        if ($value) {
            $date = \Carbon\Carbon::parse($value);
            if ($date->year > 2037) {
                // MySQL TIMESTAMP maximum range is 2038-01-19. Capped at end of 2037.
                $value = '2037-12-31 23:59:59';
            }
        }
        $this->attributes['subscription_ends_at'] = $value;
    }

    // ── İlişkiler ──────────────────────────────────

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function owner()
    {
        return $this->users()->oldest()->first();
    }

    // ── Durum Kontrolleri ──────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    public function isTrialExpired(): bool
    {
        return $this->isTrial() && $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isSubscriptionExpiring(int $days = 7): bool
    {
        return $this->subscription_ends_at
            && $this->subscription_ends_at->isBetween(now(), now()->addDays($days));
    }

    // ── Limit Kontrolleri ──────────────────────────

    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->max_users;
    }

    public function canAddProject(?int $limit = null): bool
    {
        if ($limit === null) return true;
        return $this->projects()->count() < $limit;
    }

    // ── Scopes ─────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeExpiring($query, int $days = 7)
    {
        return $query->where('subscription_ends_at', '<=', now()->addDays($days))
                     ->where('subscription_ends_at', '>=', now());
    }

    // ── Yardımcılar ────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'trial' => 'Deneme',
            'active' => 'Aktif',
            'past_due' => 'Ödeme Bekliyor',
            'suspended' => 'Askıda',
            'cancelled' => 'İptal',
            default => $this->status,
        };
    }

    public function getUserCountAttribute(): int
    {
        return $this->users()->count();
    }
}
