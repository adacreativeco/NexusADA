<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class Tool extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $guarded = [];
}
