<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('position');
            $table->string('recurrence_pattern')->nullable()->after('is_recurring'); // daily, weekly, biweekly, monthly
            $table->date('recurrence_end_date')->nullable()->after('recurrence_pattern');
            $table->foreignId('parent_task_id')->nullable()->after('recurrence_end_date')->constrained('tasks')->nullOnDelete();
            $table->datetime('last_recurrence_at')->nullable()->after('parent_task_id');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn(['is_recurring', 'recurrence_pattern', 'recurrence_end_date', 'parent_task_id', 'last_recurrence_at']);
        });
    }
};
