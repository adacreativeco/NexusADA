<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable;

class Collection extends Model implements Auditable
{
    use BelongsToTenant, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_at' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function income()
    {
        return $this->belongsTo(Income::class);
    }
}
