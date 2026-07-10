<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Aktiviteyi gerçekleştiren kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * İlişkili model
     */
    public function subject()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }
}
