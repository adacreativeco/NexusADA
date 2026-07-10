<?php

namespace App\Models\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * BelongsToTenant — Multi-tenancy global scope trait.
 *
 * Katmanlı erişim:
 * 1. Platform sahibi (tenant_id = NULL, webmaster) → TÜM VERİYİ GÖRÜR
 *    - Impersonate modundaysa → sadece hedef tenant verisini görür
 * 2. Tenant kullanıcı (tenant_id = X) → sadece kendi tenant verisini görür
 * 3. Yeni kayıtlarda tenant_id otomatik atanır
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // ── Global Scope: Tenant izolasyonu ──────────────────────
        static::addGlobalScope('tenant', function (Builder $query) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return; // Artisan komutlarında filtre uygulanmaz
            }

            $user = auth()->user();
            if (!$user) return;

            $table = $query->getModel()->getTable();

            if ($user->tenant_id) {
                // Normal tenant kullanıcı → sadece kendi verisi
                $query->where("{$table}.tenant_id", $user->tenant_id);
            } elseif (session('impersonating_tenant_id')) {
                // Platform kullanıcı impersonate modunda → hedef tenant verisi
                $query->where("{$table}.tenant_id", session('impersonating_tenant_id'));
            }
            // Platform sahibi (tenant_id = NULL, impersonate yok) → scope uygulanmaz, tüm veri görünür
        });

        // ── Creating Event: Otomatik tenant_id ataması ────────
        static::creating(function ($model) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }

            $user = auth()->user();
            if (!$user || !empty($model->tenant_id)) return;

            if ($user->tenant_id) {
                $model->tenant_id = $user->tenant_id;
            } elseif (session('impersonating_tenant_id')) {
                // Impersonate modunda oluşturulan kayıtlar hedef tenant'a ait olur
                $model->tenant_id = session('impersonating_tenant_id');
            } else {
                // Platform admini (tenant_id = null) ve impersonate değilse, ilk tenant'ı varsayılan atayalım
                $firstTenantId = cache()->remember('first_tenant_id', 3600, function () {
                    return \App\Models\Tenant::value('id') ?? 3;
                });
                $model->tenant_id = $firstTenantId;
            }
        });

        // ── Updating Event: tenant_id değiştirilmesini engelle ────────
        static::updating(function ($model) {
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }

            $user = auth()->user();
            if (!$user) return;

            // Eğer normal bir tenant kullanıcısı ise ve tenant_id değişiyorsa engelle!
            if ($user->tenant_id && $model->isDirty('tenant_id')) {
                abort(403, 'Güvenlik İhlali: Kaydın tenant_id değeri sonradan değiştirilemez.');
            }
        });
    }

    /**
     * Tenant ilişkisi
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Global scope'u devre dışı bırak (platform bileşenleri için)
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }

    /**
     * Belirli bir tenant'ın verilerini getir
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')->where('tenant_id', $tenantId);
    }
}
