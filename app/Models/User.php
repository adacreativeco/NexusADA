<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'email_verified_at',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // ── Tenant İlişkisi ─────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Kullanıcı webmaster (platform sahibi) mi?
     */
    public function isWebmaster(): bool
    {
        return $this->hasRole('webmaster') || $this->hasRole('super_admin');
    }

    /**
     * Kullanıcı herhangi bir tenant'a ait mi?
     */
    public function hasTenant(): bool
    {
        return $this->tenant_id !== null;
    }

    /**
     * Türkçe doğrulama e-postası gönder.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }
}
