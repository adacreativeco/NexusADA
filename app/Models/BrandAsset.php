<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\Traits\BelongsToTenant;

class BrandAsset extends Model implements Auditable
{
    use AuditableTrait, BelongsToTenant;
    protected $guarded = [];

    protected $casts = [
        'files' => 'array',
    ];

    public function setFilesAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['files'] = json_encode([$value]);
        } else {
            $this->attributes['files'] = json_encode($value);
        }
    }
}
