<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable;

class Income extends Model implements Auditable
{
    use BelongsToTenant, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->tax_amount = round($model->amount * ($model->tax_rate / 100), 2);
            $model->grand_total = $model->amount + $model->tax_amount;
        });

        static::creating(function ($model) {
            if (empty($model->income_number)) {
                $model->income_number = static::generateIncomeNumber();
            }
        });
    }

    public static function generateIncomeNumber(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('GEL-%d-%04d', $year, $count);
    }

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

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function getCollectedAmountAttribute(): float
    {
        return $this->collections()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0.00, $this->grand_total - $this->collected_amount);
    }
}
