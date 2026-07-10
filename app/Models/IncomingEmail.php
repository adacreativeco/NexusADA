<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class IncomingEmail extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'received_at'     => 'datetime',
        'has_attachments' => 'boolean',
    ];

    // ── Relationships ───────────────────────────────────

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeForAccount($query, int $accountId)
    {
        return $query->where('email_account_id', $accountId);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('subject', 'like', "%{$term}%")
              ->orWhere('from_address', 'like', "%{$term}%")
              ->orWhere('from_name', 'like', "%{$term}%")
              ->orWhere('body', 'like', "%{$term}%");
        });
    }

    // ── Helpers ─────────────────────────────────────────

    public function markRead(): void
    {
        if ($this->status === 'unread') {
            $this->update(['status' => 'read']);
        }
    }

    public function getFromDisplayAttribute(): string
    {
        return $this->from_name
            ? "{$this->from_name} <{$this->from_address}>"
            : $this->from_address;
    }
}

