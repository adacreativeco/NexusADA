<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class Task extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
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

    public function activeTimer()
    {
        return $this->timeLogs()->running()->where('user_id', auth()->id())->first();
    }

    public function hasRunningTimer(): bool
    {
        return $this->timeLogs()->running()->where('user_id', auth()->id())->exists();
    }

    public function totalTimeMinutes(): int
    {
        return (int) $this->timeLogs()->whereNotNull('stopped_at')->sum('duration_minutes');
    }

    public function getTotalTimeFormattedAttribute(): string
    {
        $minutes = $this->totalTimeMinutes();
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return $hours > 0 ? "{$hours}s {$mins}dk" : "{$mins}dk";
    }

    // ── Recurring Tasks ───────────────────────────────

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function generatedTasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function isRecurring(): bool
    {
        return (bool) $this->is_recurring;
    }

    public function scopeRecurringTemplates($query)
    {
        return $query->where('is_recurring', true)->whereNull('parent_task_id');
    }

    public function nextRecurrenceDate(): ?\Carbon\Carbon
    {
        $last = $this->last_recurrence_at ? \Carbon\Carbon::parse($this->last_recurrence_at) : \Carbon\Carbon::parse($this->created_at);

        return match ($this->recurrence_pattern) {
            'daily' => $last->copy()->addDay(),
            'weekly' => $last->copy()->addWeek(),
            'biweekly' => $last->copy()->addWeeks(2),
            'monthly' => $last->copy()->addMonth(),
            default => null,
        };
    }
}
