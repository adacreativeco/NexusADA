<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class AiUsageLog extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_usage_logs';

    public $timestamps = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
