<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use App\Models\Traits\Approvable;
use OwenIt\Auditing\Contracts\Auditable;

class Expense extends Model implements Auditable
{
    use BelongsToTenant, Approvable, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->tax_amount = round($model->amount * ($model->tax_rate / 100), 2);
            $model->grand_total = $model->amount + $model->tax_amount;
        });

        static::creating(function ($model) {
            if (empty($model->expense_number)) {
                $model->expense_number = static::generateExpenseNumber();
            }
        });
    }

    public static function generateExpenseNumber(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('GID-%d-%04d', $year, $count);
    }

    /**
     * Override Approvable approved status target
     */
    public function getApprovedStatus(): string
    {
        return 'approved_internal';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
