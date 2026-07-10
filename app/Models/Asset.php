<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class Asset extends Model
{
    use BelongsToTenant;

    protected $table = 'assets';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
