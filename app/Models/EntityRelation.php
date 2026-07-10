<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class EntityRelation extends Model
{
    use BelongsToTenant;

    protected $table = 'entity_relations';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function source()
    {
        return $this->morphTo();
    }

    public function target()
    {
        return $this->morphTo();
    }

    /**
     * Add a relation helper
     */
    public static function relate(Model $source, Model $target, string $type, array $metadata = []): self
    {
        return static::firstOrCreate([
            'tenant_id' => $source->tenant_id ?? target_tenant_id($source, $target),
            'source_type' => get_class($source),
            'source_id' => $source->getKey(),
            'target_type' => get_class($target),
            'target_id' => $target->getKey(),
            'relation_type' => $type,
        ], [
            'metadata' => $metadata,
        ]);
    }
}

function target_tenant_id($source, $target)
{
    return $source->tenant_id ?? $target->tenant_id ?? auth()->user()->tenant_id ?? 1;
}
