<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class SocialPost extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
