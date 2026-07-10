<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class Interaction extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'interaction_date' => 'datetime',
        'follow_up_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
