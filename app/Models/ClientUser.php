<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ClientUser extends Authenticatable
{
    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'notifications_enabled' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
