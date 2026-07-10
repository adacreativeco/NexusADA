<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static $requestCache = null;

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$requestCache === null) {
            self::$requestCache = cache()->remember('platform_settings_all', 3600, function () {
                return static::pluck('value', 'key')->toArray();
            });
        }

        return self::$requestCache[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget('platform_settings_all');
        cache()->forget('desktop_version');
        self::$requestCache = null;
    }

    /**
     * Get desktop version info for the API endpoint.
     */
    public static function getDesktopVersion(): array
    {
        return [
            'version' => static::get('desktop_latest_version', '1.0.0'),
            'download_url' => static::get('desktop_download_url', ''),
            'release_notes' => static::get('desktop_release_notes', ''),
            'is_mandatory' => (bool) static::get('desktop_update_mandatory', false),
            'released_at' => static::get('desktop_released_at', now()->toIso8601String()),
        ];
    }
}
