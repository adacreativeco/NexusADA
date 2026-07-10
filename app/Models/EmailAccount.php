<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailAccount extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'password'   => 'encrypted',
        'is_active'  => 'boolean',
        'last_sync_at' => 'datetime',
        'imap_port'  => 'integer',
        'sync_interval_minutes' => 'integer',
    ];

    protected $hidden = ['password'];

    // ── Relationships ───────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomingEmails(): HasMany
    {
        return $this->hasMany(IncomingEmail::class);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForSync($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('last_sync_at')
                  ->orWhereRaw(config('database.default') === 'sqlite' 
                      ? 'last_sync_at <= datetime("now", "-" || sync_interval_minutes || " minutes")'
                      : 'last_sync_at <= DATE_SUB(NOW(), INTERVAL sync_interval_minutes MINUTE)');
            });
    }

    // ── Helpers ─────────────────────────────────────────────

    public function markSynced(): void
    {
        $this->update([
            'last_sync_at' => now(),
            'last_error'   => null,
        ]);
    }

    public function markError(string $message): void
    {
        $this->update([
            'last_error' => $message,
        ]);
    }

    /**
     * Get IMAP config array for webklex/laravel-imap.
     */
    public function toImapConfig(): array
    {
        return [
            'host'          => $this->imap_host,
            'port'          => $this->imap_port,
            'encryption'    => $this->imap_encryption,
            'validate_cert' => true,
            'username'      => $this->username,
            'password'      => $this->password, // auto-decrypted by cast
            'protocol'      => 'imap',
        ];
    }
}
