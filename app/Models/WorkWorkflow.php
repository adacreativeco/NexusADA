<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class WorkWorkflow extends Model
{
    use BelongsToTenant;

    protected $table = 'work_workflows';

    protected $guarded = [];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
