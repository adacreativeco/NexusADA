<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class Campaign extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class);
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
}
