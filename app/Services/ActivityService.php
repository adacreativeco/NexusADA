<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    /**
     * Log a new activity.
     *
     * @param string $title
     * @param string|null $description
     * @param string $type (user, ai, automation, system)
     * @param Model|null $model
     * @param array $properties
     * @param int|null $tenantId
     * @param int|null $userId
     * @return Activity
     */
    public static function log(
        string $title,
        ?string $description = null,
        string $type = 'user',
        ?Model $model = null,
        array $properties = [],
        ?int $tenantId = null,
        ?int $userId = null
    ): Activity {
        // Resolve tenant_id
        if (!$tenantId) {
            if ($model && isset($model->tenant_id)) {
                $tenantId = $model->tenant_id;
            } else {
                $tenantId = Auth::user()?->tenant_id ?? session('tenant_id');
            }
        }

        // Resolve user_id
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        // Clean model details
        $modelType = null;
        $modelId = null;
        if ($model) {
            $modelType = get_class($model);
            $modelId = $model->getKey();
        }

        // Create the activity in the database
        return Activity::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId ?? 0,
            'user_id' => $userId,
            'activity_type' => $type,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'title' => $title,
            'description' => $description,
            'properties' => $properties,
        ]);
    }

    public static function logUser(string $title, ?string $description = null, ?Model $model = null, array $properties = []): Activity
    {
        return self::log($title, $description, 'user', $model, $properties);
    }

    public static function logAI(string $title, ?string $description = null, ?Model $model = null, array $properties = []): Activity
    {
        return self::log($title, $description, 'ai', $model, $properties);
    }

    public static function logAutomation(string $title, ?string $description = null, ?Model $model = null, array $properties = []): Activity
    {
        return self::log($title, $description, 'automation', $model, $properties);
    }

    public static function logSystem(string $title, ?string $description = null, ?Model $model = null, array $properties = []): Activity
    {
        return self::log($title, $description, 'system', $model, $properties);
    }
}
