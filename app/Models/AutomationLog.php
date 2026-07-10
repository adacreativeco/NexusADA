<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class AutomationLog extends Model
{
    use BelongsToTenant;

    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function rule()
    {
        return $this->belongsTo(AutomationRule::class, 'rule_id');
    }
}
