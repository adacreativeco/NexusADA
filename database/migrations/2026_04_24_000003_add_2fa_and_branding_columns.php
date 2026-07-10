<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('remember_token');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
        });

        // ── Tenant branding columns ─────────────────────────
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('logo_path', 500)->nullable()->after('logo_url');
            $table->string('primary_color', 7)->default('#10b981')->after('logo_path');
            $table->string('company_address')->nullable()->after('address');
            $table->string('company_phone', 50)->nullable()->after('company_address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled', 'two_factor_secret',
                'two_factor_recovery_codes', 'two_factor_confirmed_at',
            ]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'primary_color', 'company_address', 'company_phone']);
        });
    }
};
