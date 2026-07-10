<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class Workflow extends Model
{
    use BelongsToTenant;

    protected $table = 'workflows';

    protected $guarded = [];

    protected $casts = [
        'steps' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workWorkflows()
    {
        return $this->hasMany(WorkWorkflow::class);
    }
}
