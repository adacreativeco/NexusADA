<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\Approvable;
use OwenIt\Auditing\Contracts\Auditable;

class Contract extends Model implements Auditable
{
    use BelongsToTenant, Approvable, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'value' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Overrides for Approvable trait
    public function getApprovedStatus(): string
    {
        return 'active';
    }

    // Polymorphic notes, documents, comments
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

    /**
     * Generate contract number in format SZL-YYYY-XXXX.
     */
    public static function generateNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "SZL-{$year}-";
        
        $last = self::where('contract_number', 'like', $prefix . '%')
            ->orderBy('contract_number', 'desc')
            ->first();

        if ($last) {
            $lastNum = intval(substr($last->contract_number, -4));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }
}
