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
        Schema::table('content_items', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->after('platform')->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('campaign_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['campaign_id', 'department_id']);
        });
    }
};
