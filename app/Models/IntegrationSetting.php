<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'events' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->whereJsonContains('events', $event);
    }
}
