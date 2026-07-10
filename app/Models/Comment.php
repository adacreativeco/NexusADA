<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'mentions' => 'array',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }

    /**
     * Parse @mentions from content and return user IDs.
     */
    public function parseMentions(): array
    {
        preg_match_all('/@(\w+)/', $this->content, $matches);
        if (empty($matches[1])) return [];

        return User::whereIn('name', $matches[1])->pluck('id')->toArray();
    }
}
