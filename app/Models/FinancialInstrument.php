<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable;

class FinancialInstrument extends Model implements Auditable
{
    use BelongsToTenant, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];
}
