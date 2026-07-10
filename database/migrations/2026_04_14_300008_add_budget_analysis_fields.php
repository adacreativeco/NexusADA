<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('hourly_rate', 8, 2)->default(0)->after('actual_revenue');
            $table->decimal('planned_hours', 6, 1)->nullable()->after('hourly_rate');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate', 'planned_hours']);
        });
    }
};
