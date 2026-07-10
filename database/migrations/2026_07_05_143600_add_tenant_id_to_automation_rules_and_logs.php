<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_rules', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
        });
        Schema::table('automation_logs', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automation_rules', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        Schema::table('automation_logs', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
