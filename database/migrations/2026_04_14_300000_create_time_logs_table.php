<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->datetime('started_at');
            $table->datetime('stopped_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->boolean('billable')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
            $table->index('task_id');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
