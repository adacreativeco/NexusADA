<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function setMaxProjectsAttribute($value)
    {
        $this->attributes['max_projects'] = $value === '' || $value === null ? 0 : $value;
    }

    public function setMaxUsersAttribute($value)
    {
        $this->attributes['max_users'] = $value === '' || $value === null ? 0 : $value;
    }

    public function setMaxStorageMbAttribute($value)
    {
        $this->attributes['max_storage_mb'] = $value === '' || $value === null ? 0 : $value;
    }

    public function setMaxCampaignsAttribute($value)
    {
        $this->attributes['max_campaigns'] = $value === '' ? null : $value;
    }

    public function setPriceMonthlyAttribute($value)
    {
        $this->attributes['price_monthly'] = $value === '' || $value === null ? 0 : (float) $value;
    }

    public function setPriceYearlyAttribute($value)
    {
        $this->attributes['price_yearly'] = $value === '' || $value === null ? 0 : (float) $value;
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Belirli bir özelliğin bu planda aktif olup olmadığını kontrol et
     */
    public function hasFeature(string $feature): bool
    {
        return (bool) ($this->features[$feature] ?? false);
    }

    /**
     * Aktif planları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
