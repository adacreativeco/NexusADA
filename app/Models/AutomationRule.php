<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class AutomationRule extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'action_config' => 'array',
        'last_executed_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(AutomationLog::class, 'rule_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
