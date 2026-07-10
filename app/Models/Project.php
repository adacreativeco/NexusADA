<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class Project extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $guarded = [];

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'due_date',
        'tenant_id',
        'client_id',
        'usage_area',
        'budget',
        'actual_revenue',
        'hourly_rate',
        'planned_hours',
    ];

    protected static function booted()
    {
        static::saving(function ($project) {
            if ($project->client_id && empty($project->title)) {
                $project->title = $project->client->name ?? 'Unknown Project';
            }
        });
    }

    /**
     * Değer ERP'si: Projenin ait olduğu müşteri
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Display name for the project (compatibility)
     */
    public function getNameAttribute()
    {
        return $this->title ?? 'Untitled Project';
    }


    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // ── Time Tracking ──────────────────────────────────

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function totalTimeMinutes(): int
    {
        return (int) $this->timeLogs()->whereNotNull('stopped_at')->sum('duration_minutes');
    }

    public function totalBillableMinutes(): int
    {
        return (int) $this->timeLogs()->whereNotNull('stopped_at')->where('billable', true)->sum('duration_minutes');
    }

    public function getTotalTimeFormattedAttribute(): string
    {
        $minutes = $this->totalTimeMinutes();
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return $hours > 0 ? "{$hours}s {$mins}dk" : "{$mins}dk";
    }

    // ── Budget Analysis ──────────────────────────────

    public function laborCost(): float
    {
        return ($this->totalTimeMinutes() / 60) * ($this->hourly_rate ?? 0);
    }

    public function profitMargin(): float
    {
        $revenue = $this->actual_revenue ?? 0;
        return $revenue > 0 ? (($revenue - $this->laborCost()) / $revenue) * 100 : 0;
    }

    public function budgetBurnRate(): float
    {
        $planned = $this->planned_hours ?? 0;
        return $planned > 0 ? (($this->totalTimeMinutes() / 60) / $planned) * 100 : 0;
    }

    public function isOverBudget(): bool
    {
        return $this->budgetBurnRate() > 100;
    }
}
