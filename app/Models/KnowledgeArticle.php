<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class KnowledgeArticle extends Model
{
    use BelongsToTenant;

    protected $table = 'knowledge_articles';

    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
