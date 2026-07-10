<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class MediaInsight extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $table = 'media_insights';
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
