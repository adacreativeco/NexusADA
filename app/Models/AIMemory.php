<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class AIMemory extends Model
{
    use BelongsToTenant;

    protected $table = 'ai_memories';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
