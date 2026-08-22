<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudStorageService
{
    public static function getDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Upload an asset with tenant isolation path
     */
    public static function uploadTenantAsset(int $tenantId, string $filename, string $content, string $folder = 'assets'): array
    {
        $safeName = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . pathinfo($filename, PATHINFO_EXTENSION);
        $path = "tenants/{$tenantId}/{$folder}/{$safeName}";

        Storage::disk(self::getDisk())->put($path, $content);

        return [
            'disk' => self::getDisk(),
            'path' => $path,
            'url' => Storage::disk(self::getDisk())->url($path),
            'size' => strlen($content),
        ];
    }

    /**
     * Get a signed or direct URL for an asset
     */
    public static function getAssetUrl(string $path, int $expiryMinutes = 60): string
    {
        if (self::getDisk() === 's3') {
            return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($expiryMinutes));
        }
        return Storage::disk(self::getDisk())->url($path);
    }
}
