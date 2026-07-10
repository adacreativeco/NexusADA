<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\Approvable;
use OwenIt\Auditing\Contracts\Auditable;

class Proposal extends Model implements Auditable
{
    use BelongsToTenant, Approvable, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
        'valid_until' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
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
     * Generate proposal number in format TKL-YYYY-XXXX.
     */
    public static function generateNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "TKL-{$year}-";
        
        $last = self::where('proposal_number', 'like', $prefix . '%')
            ->orderBy('proposal_number', 'desc')
            ->first();

        if ($last) {
            $lastNum = intval(substr($last->proposal_number, -4));
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }
}
