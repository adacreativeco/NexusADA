<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TimeLog extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
        'is_manual' => 'boolean',
        'billable' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────

    public function scopeRunning($query)
    {
        return $query->whereNull('stopped_at');
    }

    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    public function scopeForDateRange($query, $start, $end)
    {
        return $query->whereBetween('started_at', [$start, $end]);
    }

    // ── Methods ────────────────────────────────────────

    /**
     * Stop the running timer and calculate duration.
     */
    public function stop(): self
    {
        $this->stopped_at = now();
        $this->duration_minutes = (int) $this->started_at->diffInMinutes($this->stopped_at);
        $this->save();

        return $this;
    }

    /**
     * Check if this timer is currently running.
     */
    public function isRunning(): bool
    {
        return is_null($this->stopped_at);
    }

    // ── Accessors ──────────────────────────────────────

    /**
     * Human-readable duration: "2s 15dk"
     */
    public function getDurationFormattedAttribute(): string
    {
        $minutes = $this->duration_minutes ?? 0;

        if ($this->isRunning()) {
            $minutes = (int) $this->started_at->diffInMinutes(now());
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}s {$mins}dk";
        }

        return "{$mins}dk";
    }
}
