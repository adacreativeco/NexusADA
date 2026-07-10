<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable;

class BankAccount extends Model implements Auditable
{
    use BelongsToTenant, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
    ];
}
