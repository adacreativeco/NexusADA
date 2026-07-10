<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('time_logs', 'tenant_id')) {
                $table->foreignId('tenant_id')->after('id')->nullable()->constrained()->nullOnDelete();
            }
        });

        Schema::table('audits', function (Blueprint $table) {
            if (!Schema::hasColumn('audits', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->after('id')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_logs', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }
};
