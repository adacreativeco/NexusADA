<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    // ── Versioning ────────────────────────────────────

    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    public function originalDocument()
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function isPreviewable(): bool
    {
        $previewable = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/gif', 'image/webp', 'application/pdf'];
        return in_array($this->mime_type, $previewable);
    }
}
