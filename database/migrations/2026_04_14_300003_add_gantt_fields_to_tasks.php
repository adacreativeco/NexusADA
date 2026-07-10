<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('due_date');
            $table->foreignId('depends_on')->nullable()->after('start_date')->constrained('tasks')->nullOnDelete();
            $table->decimal('estimated_hours', 5, 1)->nullable()->after('depends_on');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['depends_on']);
            $table->dropColumn(['start_date', 'depends_on', 'estimated_hours']);
        });
    }
};
